<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:18
 */

namespace app\admin\controller;

use data\model\ItemModel;
use data\model\LogModel;
use data\model\StoreItemModel;
use data\model\StoreModel;
use data\model\UserModel;
use think\Db;
use think\Request;
use data\model\BuyInModel;
use data\model\BuyInStoreModel;
use data\model\BuyOutModel;
//采购单
class BuyIn extends BaseController
{
  protected $model;
  protected $userModel;
  protected $storeModel;
  protected $itemModel;
  protected  $storeItemModel;
    protected  $buyInStoreModel;
    protected  $buyOutModel;
  protected $logModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new BuyInModel();
        $this->userModel=new UserModel();
        $this->storeModel=new StoreModel();
        $this->buyInStoreModel=new BuyInStoreModel();
        $this->buyOutModel=new BuyOutModel();
        $this->itemModel=new ItemModel();
        $this->logModel=new LogModel();
        $this->storeItemModel=new StoreItemModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    //检查数据
    protected function checkData(&$data,$oldinfo=null)
    {
        $data['store_id'] = request()->post('store_id', null);
        $data['supplier'] = request()->post('supplier', null);
        $data['check_user'] = request()->post('check_user', null);
        $data['info'] = request()->post('info', null);


        $iteminfo= request()->post('item_info/a', null);


        if(!empty($data['store_id'])){
            if(!$this->storeModel->checkValid($data['store_id'])){
                return "仓库不对";
            }
        }
        if(!empty($data['check_user'])){
            if(!$this->userModel->checkValid($data['check_user'])){
                return "审核人不对";
            }
        }
        if(!empty($iteminfo)){
            foreach ($iteminfo as $key => $val) {
                if(!$this->itemModel->checkValid($val['id'])){
                    return "第".($key+1)."行商品id不对";
                }
                if(empty($val['price'])){
                    return "第".($key+1)."行价格没填";
                }
            }
            $data['item_info']=json_encode($iteminfo,JSON_UNESCAPED_UNICODE);
        }

        unsetSame($data,$oldinfo,'store_id','supplier','check_user','info','item_info');

        return SUCCESS;
    }
    //新增
    public  function add()
    {
        $data=array();
        $checkRes=$this->checkData($data,null);
        if($checkRes!=SUCCESS){
            return AjaxReturnMsg($checkRes);
        }
        if(empty($data['store_id'])){
            return AjaxReturnMsg("仓库不能为空");
        }
        if(empty($data['supplier'])){
            return AjaxReturnMsg("供应商不能为空");
        }
        if(empty($data['check_user'])){
            return AjaxReturnMsg("审核人不能为空");
        }
        if(empty($data['item_info'])){
            return AjaxReturnMsg("商品信息不能为空");
        }

        Db::startTrans();
        try {
            $data['id']=getOrderId(BUYIN_PRE,$this->uid);
            $data['check_status']=CHECK_NO;
            $data['build_time']=time();
            $data['build_user']=$this->uid;
            $data['in_store_status']=INSTORE_NO;
            $data['close_status']=DELETE_NO;
            $ret=$this->model->addOne($data);

            if($ret!=SUCCESS)
            {
                return AjaxReturn($ret);
            }
            //在途数增加
            $itemList=json_decode($data['item_info'],true);
            foreach ($itemList as $item)
            {
                $this->storeItemModel->addItem($item['id'],$item['num'],$data['store_id'],'on_way');
            }
            $this->logModel->addLog('订单:'.$data['id'].' 商品：'.$data['item_info']);

            Db::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("add buin:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }


    //废弃
    public  function  del()
    {
        $id= request()->post('id/a', '');
        if(empty($id)){
            return AjaxReturn(ID_EMPTY);
        }
        Db::startTrans();
        try{
            $list=$this->model->where('id','in',$id)->select();//获取对应的入库单
            if(empty($list)){
                return AjaxReturn(BUYIN_STORE_DEL_ID_ERROR);
            }
            foreach ($list as $key => $val) {
                if($val['close_status']==DELETE_OK){
                    return AjaxReturn(BUYIN_STORE_DEL_ID_CLOSE);
                }
                //在途数减少
                $orderid=$val["id"];
                $ret=$this->buyOutModel->where(['buy_order'=>$orderid,'close_status'=>DELETE_NO])->find();
                if(!empty($ret)){
                    return AjaxReturnMsg($orderid."关联退货单：".$ret['id']." 不能废弃,请先废弃退货单");
                }

                $ret=$this->buyInStoreModel->where(['buy_order'=>$orderid,'close_status'=>DELETE_NO])->find();
                if(!empty($ret)){
                    return AjaxReturnMsg($orderid."关联入库单：".$ret['id']." 不能废弃,请先废弃入库单");
                }


               if($val['in_store_status']==INSTORE_PART){
                    //部分入库
                    $itemList=json_decode($val['item_info'],true);
                    foreach ($itemList as $item)
                    {
                        $instore=0;
                        if(!empty($item['inStore'])){
                            $instore=$item['inStore'];
                        }
                        $this->storeItemModel->delItem($item['id'],$item['num']-$instore,$val['store_id'],'on_way');
                    }
                }
                else if($val['in_store_status']==INSTORE_NO){
                    //还没有入库,或者全部入库
                    $itemList=json_decode($val['item_info'],true);
                    foreach ($itemList as $item)
                    {
                        $this->storeItemModel->delItem($item['id'],$item['num'],$val['store_id'],'on_way');
                    }
                }

                if(SUCCESS!=$this->model->updateOne($val['id'],["close_status"=>DELETE_OK]))
                {
                    return AjaxReturn(SYSTEM_ERROR);
                }
            }
            $this->logModel->addLog('订单:'.json_encode($id));
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("del buyin:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }


    }

    //审核
    public  function  checkOk()
    {
        return $this->checkCommon();

    }

    //修改
    public  function  edit()
    {
        $data=array();
        $id = request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }

        $oldinfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldinfo))
        {
            return AjaxReturnMsg("id找不到订单");
        }
        $checkRes=$this->checkData($data,$oldinfo);
        if($checkRes!=SUCCESS){
            return AjaxReturnMsg($checkRes);
        }
        unset($data['store_id']);
        Db::startTrans();
        try{

            if(!empty($data['item_info']))
            {
                if($oldinfo['in_store_status']!=INSTORE_NO){
                    return AjaxReturnMsg("部分入库单，不能修改商品明细");
                }
                $itemList=json_decode($oldinfo['item_info'],true);
                foreach ($itemList as $item)
                {
                    $this->storeItemModel->delItem($item['id'],$item['num'],$oldinfo['store_id'],'on_way');
                }
                $itemList=json_decode($data['item_info'],true);
                foreach ($itemList as $item)
                {
                    $this->storeItemModel->addItem($item['id'],$item['num'],$oldinfo['store_id'],'on_way');
                }
            }
            $ret=$this->model->updateOne($id,$data);
            if($ret!=SUCCESS)
            {
                return AjaxReturn($ret);
            }
            $this->logModel->addLog('订单：'.$id.' 更新内容:'.json_encode($data,JSON_UNESCAPED_UNICODE));
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e)
        {
            \think\Log::record("update buyin:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }

    }

    public  function getItemInstore()
    {
        $id = request()->post('id', '');
        if(empty($id)){
            return AjaxReturnMsg("id为空");
        }
        $inStoreItem=array();
        if($this->model->GetInStoreInfo($inStoreItem,$id)!=SUCCESS)
        {
            return AjaxReturnMsg(id错误);
        }
        return AjaxReturn(SUCCESS,$inStoreItem);
    }
}