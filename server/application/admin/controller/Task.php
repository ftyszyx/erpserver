<?php
/**
 * Created by zyx.
 * Date: 2018-3-26
 * Time: 13:33
 */

namespace app\admin\controller;

\think\Loader::addNamespace('data', 'data/');

use data\model\InStoreLogModel;
use data\model\SellAssignModel;
use data\model\SellModel;
use data\model\StoreItemModel;
use data\model\StoreModel;
use think\Controller;
use data\model\DataBaseModel;
use think\Db;

class Task extends  Controller
{
    public function autoSave()
    {
        $dataBaseModel=new DataBaseModel();
        $dataBaseModel->saveData("系统备份".date('y-m-d'),false);
    }


    private  function   correctOne($order,&$fatherOkArr,&$splitOrderArr){
        $sellmodel=new SellModel();
        $sellassignModel=new  SellAssignModel();
        $fatherOrderList=$sellmodel->where(['shop_order'=>$order['shop_order'],'status'=>ASSIGN_OK])->select();
        \think\Log::record("splite order:".$order['id']." father count:".count($fatherOrderList),'zyx');
        foreach ($fatherOrderList as $fateritem)
        {
            if(in_array($fateritem['id'],$splitOrderArr)==false&&in_array($fateritem['id'],$fatherOkArr)==false){
                $assigninfo=$sellassignModel->where('order_info',"like","%".$fateritem['id']."%")->find();
                if(empty($assigninfo)){
                    \think\Log::record("father id not find:".$fateritem['id'],'zyx');
                    return false;
                }else{
                    $orderininfo=$sellassignModel->where('order_info',"like","%".$order['id']."%")->find();
                    if(!empty($orderininfo)){
                        \think\Log::record(sprintf("order id %s in assing %s",$order['id'],$assigninfo['id']),'zyx');
                        return false;
                    }
                    $sellOrderList=json_decode($assigninfo['order_info'],true);
                    $sellOrderList[]=$order['id'];
                    $sellassignModel->updateOne($assigninfo['id'],['order_info'=>json_encode($sellOrderList)]);
                    $sellmodel->updateOne($order['id'],['assign_order'=>$assigninfo["id"]]);
                    $fatherOkArr[]=$fateritem['id'];
                    \think\Log::record("asssing order:".$order['id']." to father order:".$fateritem['id']." to asssing order:".$assigninfo["id"],'zyx');
                    return true;
                }
            }
        }
        return true;
    }
    public  function  correctData(){
        $sellmodel=new SellModel();
        Db::startTrans();
        try{

            $splitOrderList=$sellmodel->where("id",'like','SO1804241500__011____')->select();
            $splitOrderArr=array();
            $fatherOkArr=array();
            foreach ( $splitOrderList as $order)
            {
                $splitOrderArr[]=$order["id"];
            }
            foreach ( $splitOrderList as $order) {
                if($order['status']==ASSIGN_OK){

                    if(false==$this->correctone($order,$fatherOkArr,$splitOrderArr))
                    {
                        return  AjaxReturn(SYSTEM_ERROR);;
                    }
                }else{
                    \think\Log::record("order not assign:".$order['id'],'zyx');
                }

            }
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("assign:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }
    }

    private  function  copyStoreItem($model,$searchArr,$dstid){
        $limit=100;
        $num=0;
        $totalnum=$model->where($searchArr)->count();
       $pagenum=intval($totalnum/$limit);
        for ($i=0;$i<$pagenum+1;$i++){
             $start=$i*$limit;
             $data = $model->where($searchArr)->limit($start.','.$limit)->select();
              foreach ($data as $key=>$value){
                $item=$value->toArray();
                $item['id']=null;
                $item['store_id']=$dstid;
                $model->addOne($item);
                $num++;
                  \think\Log::record(sprintf("add item:%d",$num),'zyx');
            }
        }
        return $num;
    }
    //复制仓库
    //php index.php admin/task/copyStore >>ab.log
    public  function copyStore()
    {
        $srcid=8;
        $dstid=14;
        \think\Log::record(sprintf("copy src:%s to dst:%s",$srcid,$dstid),'zyx');
       $storeItemModel=new StoreItemModel();
       $storeLogModel=new InStoreLogModel();
        Db::startTrans();
        try{
            \think\Log::record(sprintf("reset dst store data"),'zyx');
            $storeItemModel->where(['store_id'=>$dstid])->delete();
            $storeLogModel->where(['store_id'=>$dstid])->delete();
            \think\Log::record(sprintf("copy storeitem"),'zyx');
            $num=$this->copyStoreItem($storeItemModel,['store_id'=>$srcid],$dstid);
            \think\Log::record(sprintf("add item:%d",$num),'zyx');
            \think\Log::record(sprintf("copy storelog"),'zyx');
            $num=$this->copyStoreItem($storeLogModel,['store_id'=>$srcid],$dstid);
            \think\Log::record(sprintf("add item log:%d",$num),'zyx');
            DB::commit();
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("copy error:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }

    }
}