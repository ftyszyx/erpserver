<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:17
 */

namespace app\admin\controller;

use data\model\InStoreLogModel;
use data\model\ItemModel;
use data\model\LogModel;
use data\model\StoreItemModel;
use data\model\StoreModel;
use data\model\UserModel;
use think\Db;
use think\Request;
use data\model\StoreChangeModel;

class StoreChange extends BaseController
{
    protected $model;
    protected  $storeItemModel;
    protected  $userModel;
    protected  $itemModel;
    protected  $logModel;
    protected  $storeModel;
    protected  $inStoreLogModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new StoreChangeModel();
        $this->storeItemModel=new StoreItemModel();
        $this->userModel=new UserModel();
        $this->itemModel=new ItemModel();
        $this->storeModel=new StoreModel();
        $this->logModel=new LogModel();
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
        $data['in_store'] = request()->post('in_store', null);
        $data['out_store'] = request()->post('out_store', null);
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

        unsetSame($data,$oldinfo,'check_user','in_store','out_store','info');

        return SUCCESS;
    }

    //做一比调库的操作 $in_store 调入仓库  $out_store 调出仓库
    private function DoStoreOne($itemInfo,$in_store,$out_store,$type,$op_type,$orderid)
    {
        foreach ($itemInfo as $key=>$value){
            $itemId=$value['id'];
            $num=$value['num'];
            if($type=='add'){
                $this->inStoreLogModel->addOneLog($op_type,$orderid,$in_store,$itemId,$num,true);
                $ret =$this->storeItemModel->addItem($itemId,$num,$in_store,'in_store');
            }
            else
            {
                $this->inStoreLogModel->addOneLog($op_type,$orderid,$in_store,$itemId,$num,false);
                $ret =$this->storeItemModel->delItem($itemId,$num,$in_store,'in_store');
            }
            if(SUCCESS!=$ret){
                return $ret;
            }
            if($type=='add'){
                $this->inStoreLogModel->addOneLog($op_type,$orderid,$out_store,$itemId,$num,false);
                $ret =$this->storeItemModel->delItem($itemId,$num,$out_store,'in_store');
            }
            else
            {
                $this->inStoreLogModel->addOneLog($op_type,$orderid,$out_store,$itemId,$num,true);
                $ret =$this->storeItemModel->addItem($itemId,$num,$out_store,'in_store');
            }
            if(SUCCESS!=$ret){
                return $ret;
            }
        }
        return SUCCESS;
    }

    private  function  checkSameStore($instore,$outStore)
    {
        $instoreInfo=$this->storeModel->where(['id'=>$instore])->find();
        $outStoreInfo=$this->storeModel->where(['id'=>$outStore])->find();
        if(!empty($instoreInfo['same_store'])) {
            if($instoreInfo['same_store']==$outStore){
                return AjaxReturnMsg("关联仓库不能互调");
            }
        }
        if(!empty($outStoreInfo['same_store'])) {
            if($outStoreInfo['same_store']==$instore){
                return AjaxReturnMsg("关联仓库不能互调");
            }
        }
        return SUCCESS;
    }

    public  function add()
    {
        $data=array();
        $checkRes=$this->checkData($data,null);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        if(empty($data['check_user'])){
            return AjaxReturn(BUYIN_USER_EMPTY);
        }
        if(empty($data['in_store'])){
            return AjaxReturn(STORE_In_EMPTY);
        }
        if(empty($data['out_store'])){
            return AjaxReturn(STORE_OUT_EMPTY);
        }
        if(empty($data['item_info'])){
            return AjaxReturn(BUYIN_ITEM_EMPTY);
        }

        if($data['in_store']==$data['out_store']){
            return AjaxReturn(STORE_STORE_SAME);
        }

        $ret=$this->checkSameStore($data['out_store'],$data['in_store']);
        if($ret!=SUCCESS)
        {
            return $ret;
        }

        $data['id']=getOrderId(STORECHANGE_PRE,$this->uid);
        $data['check_status']=CHECK_NO;
        $data['build_time']=time();
        $data['build_user']=$this->uid;
        $data['close_status']=DELETE_NO;

        DB::startTrans();
        try{

            $ret=$this->model->addOne($data);
            if($ret!=SUCCESS){
                return AjaxReturn($ret);
            }
            $itemInfo= request()->post('item_info/a', '');


            $ret=$this->DoStoreOne($itemInfo,$data['in_store'],$data['out_store'],'add',CHANGESTORE_TYPE,$data['id']);
             if(SUCCESS!=$ret){
                 return AjaxReturn($ret);
             }
             $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("add item:".$e->getMessage()." lines:".var_export($e->getLine(),true),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }

    }

    //审核
    public  function  checkOK()
    {
        return $this->checkCommon();
    }

    //废弃
    public  function  del()
    {
        $id= request()->post('id/a', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }

        DB::startTrans();
        try{
            $list=$this->model->where('id','in',$id)->select();//获取对应的入库单
            if(empty($list)){
                return AjaxReturn(STORE_CHANGE_ID_ERROR);
            }
            //更新仓库物品
            foreach ($list as $key => $val) {
                if($val['close_status']==DELETE_OK){
                    return AjaxReturn(BUYIN_STORE_DEL_ID_CLOSE);
                }
                $itemInfo=json_decode($val['item_info'],true);
                $ret=$this->DoStoreOne($itemInfo,$val['in_store'],$val['out_store'],'del',CHANGESTORE_DEL_TYPE,$val['id']);
                if(SUCCESS!=$ret)
                {
                    return AjaxReturn($ret);
                }
            }
            //更新订单状态
            foreach ($id as $key => $val) {
                if(SUCCESS!=$this->model->updateOne($val,["close_status"=>DELETE_OK]))
                {
                    return AjaxReturn(SYSTEM_ERROR);
                }
            }
            DB::commit();
            $this->logModel->addLog(json_encode($id,JSON_UNESCAPED_UNICODE));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("del item:".$e->getMessage()." lines:".var_export($e->getTrace(),true),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }

    }
    //更新
    public  function  edit()
    {

        $data=array();
        $id = request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $oldInfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldInfo)){
            return AjaxReturn(STORE_CHANGEID_ERROR);
        }
        $checkRes=$this->checkData($data);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        //判断两个仓库是否是一样的
        $inStoreID=$oldInfo['in_store'];
        if(!empty($data['in_store'])){
            $inStoreID=$data['in_store'];
        }
        $outStoreID=$oldInfo['out_store'];
        if(!empty($data['out_store'])){
            $outStoreID=$data['out_store'];
        }

        if($inStoreID==$outStoreID){
            return AjaxReturn(STORE_STORE_SAME);
        }

        $ret=$this->checkSameStore($inStoreID,$outStoreID);
        if($ret!=SUCCESS)
        {
            return $ret;
        }

        DB::startTrans();
        try{
            $itemInfo= json_decode($oldInfo['item_info'],true);
            //先恢复
            //\think\Log::record("update item1:".var_export($itemInfo,true),'zyx');
            $ret=$this->DoStoreOne($itemInfo,$oldInfo['in_store'],$oldInfo['out_store'],'del',CHANGESTORE_UPDATE_TYPE,$oldInfo['id']);
            if(SUCCESS!=$ret){
                return $ret;
            }
            //再更新仓库
            $itemInfo= json_decode($data['item_info'],true);
            //\think\Log::record("update item:2".var_export($itemInfo,true),'zyx');
            $ret=$this->DoStoreOne($itemInfo,$oldInfo['in_store'],$oldInfo['out_store'],'add',CHANGESTORE_UPDATE_TYPE,$oldInfo['id']);
            if(SUCCESS!=$ret){
                return $ret;
            }
            $ret=$this->model->updateOne($id,$data);
            if(SUCCESS!=$ret){
                return $ret;
            }
            DB::commit();
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("update item:".$e->getMessage()." lines:".var_export($e->getLine(),true)." file:".var_export($e->getFile(),true),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }

    }
}