<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:18
 */

namespace app\admin\controller;

use data\model\BuyInModel;
use data\model\BuyOutModel;
use data\model\InStoreLogModel;
use data\model\LogModel;
use data\model\StoreItemModel;
use think\Db;
use think\Request;
use data\model\BuyInStoreModel;
use data\model\ItemModel;
use data\model\StoreModel;
use data\model\UserModel;

class BuyInStore extends BaseController
{
    protected $model;
    protected $userModel;
    protected $storeModel;
    protected $itemModel;
    protected $storeItemModel;
    protected  $buyInModel;
    protected  $buyOutModel;
    protected  $inStoreLogModel;
    protected  $logModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new BuyInStoreModel();
        $this->userModel=new UserModel();
        $this->storeModel=new StoreModel();
        $this->itemModel=new ItemModel();
        $this->storeItemModel=new StoreItemModel();
        $this->buyInModel=new BuyInModel();
        $this->logModel=new LogModel();
        $this->buyOutModel=new BuyOutModel();
        $this->inStoreLogModel=new InStoreLogModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    //检查数据
    private function checkData(&$data,$oldinfo=null)
    {
        $data['check_user'] = request()->post('check_user', null);
        $data['buy_order'] = request()->post('buy_order', null);
        $data['store_id'] = request()->post('store_id', null);
        $iteminfo= request()->post('item_info/a', null);
        $data['info'] = request()->post('info', null);

        if(!empty($data['check_user'])){
            if(!$this->userModel->checkValid($data['check_user'])){
                return BUYIN_USER_ERROR;
            }
        }
        if(!empty($iteminfo)){
            foreach ($iteminfo as $key => $val) {
                if(!$this->itemModel->checkValid($val['id'])){
                    return BUYIN_ITEM_ERROR;
                }
            }
            $data['item_info']=json_encode($iteminfo,JSON_UNESCAPED_UNICODE);
        }

        unsetSame($data,$oldinfo,'store_id','buy_order','check_user','info','item_info');

        return SUCCESS;
    }


    public  function  add(){
        return $this->addCommon('in');
    }

    public  function  del(){
        return $this->delCommon('in');
    }

    //审核
    public  function  checkOk()
    {
        return $this->checkCommon();
    }

    public  function  edit()
    {
        return $this->updateCommon('in');
    }

    //新建  in out
    protected  function addCommon($type)
    {
        $data=array();
        $checkRes=$this->checkData($data);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        if(empty($data['check_user'])){
            return AjaxReturnMsg("审核人不能为空");
        }
        if(empty($data['buy_order'])){
            return AjaxReturnMsg("采购单不能为空");
        }
        if(empty($data['item_info'])){
            return AjaxReturnMsg("商品不能为空");
        }
        if(empty($data['store_id'])){
            return AjaxReturnMsg("仓库不能为空");
        }
        if($type=="in"){
            $data['id']=getOrderId(BUYINSTORE_PRE,$this->uid);
        }
        else{
            $data['id']=getOrderId(BUYOUT_PRE,$this->uid);
        }


        $data['check_status']=CHECK_NO;
        $data['build_time']=time();
        $data['build_user']=$this->uid;
        $data['close_status']=DELETE_NO;

        DB::startTrans();
        try{
            $AddItemInfo= request()->post('item_info/a', '');
            $this->model->addOne($data);//增加入库数量
            //修改仓库信息
            foreach ($AddItemInfo as $key=>$value){
                $itemid=$value['id'];
                $num=$value['num'];
                if($type=="in"){
                    //入库单新增
                    $this->inStoreLogModel->addOneLog(INSTORE_TYPE,$data['id'],$data['store_id'],$itemid,$num,true);
                    $this->storeItemModel->addItem($itemid,$num,$data['store_id'],'in_store');
                    $this->storeItemModel->delItem($itemid,$num,$data['store_id'],'on_way');
                }
                else{
                     //退货单新增
                    $this->inStoreLogModel->addOneLog(OUTSTORE_TYPE,$data['id'],$data['store_id'],$itemid,$num,false);
                    $this->storeItemModel->delItem($itemid,$num,$data['store_id'],'in_store');
                }
            }
            if($type=="in")
            {
                //修改采购单状态
                $ret=$this->buyInModel->updateInStoreState($data['buy_order']);
                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }
            }
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
            DB::commit();

            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){

            \think\Log::record("add item:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }

    }


    //废弃
    protected  function  delCommon($type)
    {
        $id= request()->post('id/a', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        Db::startTrans();
        try{

            //仓库信息到时更新
            $list=$this->model->where('id','in',$id)->select();//获取对应的入库单
            if(empty($list)){
                return AjaxReturn(BUYIN_STORE_DEL_ID_ERROR);
            }
            $orderArr= Array();
            foreach ($list as $key => $val)
            {
                \think\Log::record("one item",'zyx');
                if($val['close_status']==DELETE_OK){
                    return AjaxReturnMsg("订单已经废弃");
                }
                $orderId=$val['buy_order'];
                $storeid=$val['store_id'];
                if(isset($orderArr[$orderId])==false){
                    $orderArr[]=$orderId;
                }
                $itemlist=json_decode($val['item_info'],true);
                foreach ($itemlist as $key=>$iteminfo){
                    $itemid=$iteminfo['id'];
                    $num=$iteminfo['num'];
                    if($type=="in"){
                        //入库单废弃
                        $ret=$this->buyOutModel->where(['buy_order'=>$orderId,'close_status'=>DELETE_NO])->select();
                        if(!empty($ret)){
                            return AjaxReturnMsg($val['id']."关联采购单：".$orderId."有退货单关联，不能废弃,请先废弃退货单");
                        }
                        $this->inStoreLogModel->addOneLog(INSTORE_DEL_TYPE,$val['id'],$storeid,$itemid,$num,false);
                        $this->storeItemModel->delItem($itemid,$num,$storeid,'in_store');
                        $this->storeItemModel->addItem($itemid,$num,$storeid,'on_way');
                    }
                    else
                    {
                        //退货单废弃
                        $this->inStoreLogModel->addOneLog(OUTSTORE_DEL_TYPE,$val['id'],$storeid,$itemid,$num,true);
                        $this->storeItemModel->addItem($itemid,$num,$storeid,'in_store');

                    }

                }

            }

            //更新入库单
            foreach ($id as $key => $val) {
                if(SUCCESS!=$this->model->updateOne($val,["close_status"=>DELETE_OK]))
                {
                    return AjaxReturn(SYSTEM_ERROR);
                }
            }

            if($type=="in")
            {
                //更新采购单状态
                foreach ($orderArr as $key=>$value)
                {
                    $ret=$this->buyInModel->updateInStoreState($value);
                    if($ret!=SUCCESS){
                        return AjaxReturn($ret);
                    }
                }
            }
            $this->logModel->addLog("ids:".json_encode($id));
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("add item".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }

    }


    //修改 in out
    protected  function  updateCommon($type)
    {

        $data=array();
        $id = request()->post('id', '');


        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }

        $oldinfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldinfo)){
            return AjaxReturn(SYSTEM_ERROR);
        }

        $checkRes=$this->checkData($data,$oldinfo);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }

        $iteminfo= request()->post('item_info/a', '');

        DB::startTrans();
        try{
            if(!empty($iteminfo)) {
                $old_iteminfo = json_decode($oldinfo['item_info'], true);
                //去掉旧库存
                if (!empty($old_iteminfo)) {
                    foreach ($old_iteminfo as $key => $value) {
                        $itemid = $value['id'];
                        $num = $value['num'];
                        if ($type == 'in') {
                            $ret = $this->buyOutModel->where(['buy_order' => $oldinfo['buy_order'], 'close_status' => DELETE_NO])->select();
                            if (!empty($ret)) {
                                return AjaxReturnMsg($id . "关联采购单：" . $oldinfo['buy_order'] . "有退货单关联，不能修改,请先废弃退货单");
                            }
                            $this->inStoreLogModel->addOneLog(INSTORE_UPDATE_TYPE, $id, $oldinfo['store_id'], $itemid, $num, false);
                            $this->storeItemModel->delItem($itemid, $num, $oldinfo['store_id'], 'in_store');
                            $this->storeItemModel->addItem($itemid, $num, $oldinfo['store_id'], 'on_way');
                        } else {
                            $this->inStoreLogModel->addOneLog(OUTSTORE_UPDATE_TYPE, $id, $oldinfo['store_id'], $itemid, $num, true);
                            $this->storeItemModel->addItem($itemid, $num, $oldinfo['store_id'], 'in_store');
                        }
                    }
                }

                //增加新库存
                foreach ($iteminfo as $key => $value) {
                    $itemid = $value['id'];
                    $num = $value['num'];
                    if ($type == 'in') {
                        $this->inStoreLogModel->addOneLog(INSTORE_UPDATE_TYPE, $id, $oldinfo['store_id'], $itemid, $num, true);
                        $this->storeItemModel->addItem($itemid, $num, $oldinfo['store_id'], 'in_store');
                        $this->storeItemModel->delItem($itemid, $num, $oldinfo['store_id'], 'on_way');
                    } else {
                        $this->inStoreLogModel->addOneLog(OUTSTORE_UPDATE_TYPE, $id, $oldinfo['store_id'], $itemid, $num, false);
                        $this->storeItemModel->delItem($itemid, $num, $oldinfo['store_id'], 'in_store');
                    }

                }
                //更新入库单
                $ret=$this->model->updateOne($id,$data);
                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }
                if($type=='in'){
                    //更新采购单
                    $ret=$this->buyInModel->updateInStoreState($oldinfo['buy_order']);
                    if($ret!=SUCCESS){
                        return AjaxReturn($ret);
                    }
                }
            }else{
                //更新入库单
                $ret=$this->model->updateOne($id,$data);
                if($ret!=SUCCESS){
                    return AjaxReturn($ret);
                }
            }

            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("add item".$e->getMessage()." lines:".var_export($e->getLine(),true),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }

    }
}