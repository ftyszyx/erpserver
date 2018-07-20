<?php
/**
 * Created by zyx.
 * Date: 2018-2-1
 * Time: 13:25
 */

namespace app\admin\controller;

use data\model\ConfigModel;
use data\model\InStoreLogModel;
use data\model\LogModel;
use data\model\SellModel;
use data\model\SellAssignModel;
use data\model\StoreItemModel;
use think\Db;
use think\Request;
use think\Session;

class SellAssign extends  BaseController
{
    protected  $model;
    protected  $userModel;
    protected  $storeModel;
    protected  $configModel;
    protected $sellModel;
    protected  $logModel;
    protected  $storeItemModel;
    protected  $inStoreLogModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        $this->model=new SellAssignModel();
        $this->sellModel=new SellModel();
        $this->configModel=new ConfigModel();
        $this->logModel=new LogModel();
        $this->storeItemModel=new StoreItemModel();
        $this->inStoreLogModel=new InStoreLogModel();
    }

    //获取所有配货单
    public  function all()
    {
        return $this->model->allList();
    }

    public  function  exportCsv()
    {
        $search=request()->post('search/a', null); //标题名
        $headList=request()->post('headlist/a', ''); //标题名
        $filename=request()->post('filename', '');  //文件名
        $nameList=request()->post('namelist/a', ''); //字段名

        Session::set('search',$search);
        Session::set('headlist',$headList);
        Session::set('filename',$filename);
        Session::set('namelist',$nameList);
        Session::set('model','SellAssign');
        Session::set('modelName','data\model\SellModel');
        return AjaxReturn(SUCCESS);
    }

    //配货 对应的销售订单 变为配货状态，同时仓库商品对应减少
    public function assignItem(){
        $id= request()->post('id/a', '');
        $info= request()->post('info', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        Db::startTrans();
        try{
            $orderList=$this->sellModel->lock(true)->where('id','in',$id)->select();
            if(empty($orderList)){
                return AjaxReturn(SYSTEM_ERROR);
            }
            foreach ($orderList as $key =>$value)
            {
                if($value['refund_status']!=REFUND_NO){
                    return AjaxReturnMsg($value['id']."为退货订单，配货失败");
                }
                if($value['status']!=CHECK_OK){
                    return AjaxReturnMsg($value['id']."订单状态不对，配货失败");
                }
            }

            $order_id=getOrderId(SELLOUT_PRE,$this->uid);

            //更新状态
            $ret=$this->sellModel->where('id','in',$id)->update(["status"=>ASSIGN_OK,'assign_order'=>$order_id]);
            if(empty($ret)){
                return AjaxReturn(SYSTEM_ERROR);
            }

            //仓库商品对应减少
            $total_num=0;
            $list=$this->sellModel->where('id','in',$id)->select();
            foreach ($list as $key => $val) {
               $itemId=$val['item_id'];
                $num=$val['num'];
                $total_num+=$num;
                if(empty($store_id)){
                    $store_id=$val['store_id'];
                }
                else{
                    if($store_id!=$val['store_id']){
                        return AjaxReturnMsg("订单：{$val['id']} 与其它的订单仓库不一至");
                    }
                }
                $this->inStoreLogModel->addOneLog(SELLOUT_TYPE,$order_id,$store_id,$itemId,$num,false);
                $ret=$this->storeItemModel->delItem($itemId,$num,$store_id,'in_store');

                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }
                $ret=$this->storeItemModel->delItem($itemId,$num,$store_id,'in_sale');
                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }
            }

            //增加一个配货单
            $data=array();
            $data['id']=$order_id;
            $data['build_time']=time();
            $data['build_user']=$this->uid;
            $data['close_status']=DELETE_NO;
            $data['info']=$info;
            $data['store_id']=$store_id;
            $data['total_num']=$total_num;
            $data['order_info']=json_encode($id);
            $this->model->insert($data);
            DB::commit();
            $this->logModel->addLog(json_encode($id));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("assign error:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }
    }



    //反审核  对应的销售订单回退到未配货状态，同时仓库商品对应增加 同时对应的配货单作废
    public function  del(){
        $id= request()->post('id', '');
        $delinfo= request()->post('del_info', '');
        if(empty($id)){
            return AjaxReturn(BUYIN_USER_EMPTY);
        }
        if(empty($delinfo)){
            return AjaxReturnMsg("废弃原因不能为空");
        }
        Db::startTrans();
        try{
            $orderInfo=$this->model->lock(true)->where(['id'=>$id])->find();
            if(empty($orderInfo)){
                return AjaxReturn(STORE_CHANGE_ID_ERROR);
            }
            //更新配货单状态
            $this->model->where(['id'=>$id])->update(['close_status'=>DELETE_OK,'del_info'=>$delinfo]);

            $sellOrderList=json_decode($orderInfo['order_info'],true);

            foreach ($sellOrderList as $key => $val)
            {
                $orderInfo=$this->sellModel->where(['id'=>$val])->find();
                if(empty($orderInfo)){
                    return AjaxReturn(STORE_CHANGE_ID_ERROR);
                }

                if($orderInfo['status']==DELETE_OK){
                    continue;
                }
                if($orderInfo['status']!=ASSIGN_OK){
                    return AjaxReturnMsg("订单状态错误：".$val);
                }

                //更新订单状态
                $ret=$this->sellModel->where(['id'=>$val])->update(["status"=>CHECK_OK,"assign_order"=>""]);
                if(empty($ret)){
                   return AjaxReturn(STORE_CHANGE_ID_ERROR);
                }



                //仓库商品对应增加
                $this->inStoreLogModel->addOneLog(SELLOUT_DEL_TYPE,$id,$orderInfo['store_id'],$orderInfo['item_id'],$orderInfo['num'],true);
                $ret=$this->storeItemModel->addItem($orderInfo['item_id'],$orderInfo['num'],$orderInfo['store_id'],'in_store');
                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }
                //
                $ret=$this->storeItemModel->addItem($orderInfo['item_id'],$orderInfo['num'],$orderInfo['store_id'],'in_sale');
                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }

            }
            DB::commit();
            $this->logModel->addLog("出货单".$id);
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("assign:".$e->getMessage()." lines:".var_export($e->getTrace(),true),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }
    }

    //修改  只能修改备注信息
    public function edit(){
        $data=array();
        $id=request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(BUYIN_USER_EMPTY);
        }
        $data['info']=request()->post('info', '');
        $this->logModel->addLog("出货单".$id." 信息:".$data['info']);
        return AjaxReturn($this->model->updateOne($id,$data));
    }

}
