<?php
/**
 * Created by zyx.
 * Date: 2018-2-1
 * Time: 13:25
 */

namespace app\admin\controller;


use data\model\ConfigModel;
use data\model\InStoreLogModel;
use data\model\ItemModel;
use data\model\LogModel;
use data\model\SellModel;
use data\model\ShopModel;
use data\model\StoreItemModel;
use data\model\StoreModel;
use data\model\UserModel;
use think\Cache;
use think\Db;
use think\Debug;
use think\Request;
use think\Session;

class Sell extends BaseController
{
    protected  $model ;
    protected  $userModel;
    protected  $storeModel;
    protected  $configModel;
    protected  $logModel;
    protected  $shopModel;
    protected  $itemModel;
    protected $storeItemModel;
    protected  $inStoreLogModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new SellModel();
        $this->userModel=new UserModel();
        $this->storeModel=new StoreModel();
        $this->configModel=new ConfigModel();
        $this->shopModel=new ShopModel();
        $this->logModel=new LogModel();
        $this->itemModel=new ItemModel();
        $this->storeItemModel=new StoreItemModel();
        $this->inStoreLogModel=new InStoreLogModel();
    }

    //检查数据
    protected function checkData(&$data,$oldinfo=null)
    {
        $data['store_id'] = request()->post('store_id', null);
        $data['check_user'] = request()->post('check_user', null);
        $data['info'] = request()->post('info', '');

        $data['pay_time'] = request()->post('pay_time', null);
        $data['order_time'] = request()->post('order_time', null);
        //$data['sell_type'] = request()->post('sell_type', null);
        $data['track_man'] = request()->post('track_man', null);
        $data['sell_vip_info'] = request()->post('sell_vip_info', null);

        $data['user_id_number'] = request()->post('user_id_number', null);
        $data['customer_name'] = request()->post('customer_name', null);
        $data['customer_addr'] = request()->post('customer_addr', null);
        $data['user_phone'] = request()->post('user_phone', null);
        $data['logistics'] = request()->post('logistics', null);
        $data['user_info'] = request()->post('user_info', null);

        $data['idnumpic1'] = request()->post('idnumpic1', null);
        $data['idnumpic2'] = request()->post('idnumpic2', null);



         if(!empty($data['user_id_number'])){
             if(checkIdCard($data['user_id_number'])==false){
                 return SELL_IDNUMBER_ERROR;
             }
         }

        if(!empty($data['sell_type'])){
            if($data['sell_type']>SELL_TYPE_MAX||$data['sell_type']<SELL_TYPE_MIN){
                return SELL_TYPE_ERROR;
            }
        }

        if(!empty($data['store_id'])){
            if(!$this->storeModel->checkValid($data['store_id'])){
                return BUYIN_DATA_ERROR;
            }
        }
        if(!empty($data['check_user'])){
            if(!$this->userModel->checkValid($data['check_user'])){
                return BUYIN_DATA_ERROR;
            }
        }
        unsetSame($data,$oldinfo,'store_id','check_user','info',"pay_time",'order_time','user_info',
            'customer_addr','customer_name','user_id_number','user_phone','track_man','sell_vip_info','logistics','idnumpic1','idnumpic2');
        return SUCCESS;
    }

    //获取所有
    public  function all()
    {

        //Debug::remark('behavior_start', 'time');
        $ret= $this->model->allList();
        //Debug::remark('behavior_end', 'time');
        //\think\Log::record(' [ RunTime:' . Debug::getRangeTime('behavior_start', 'behavior_end') . 's ]', 'zyx');
        return $ret;
    }

    //获取总数
    public  function  getTotalNum($search)
    {
        $num=0;
        $list=$this->model->where($search)->select();
        foreach ($list as  $item){
            $num+=$item['num'];
        }
        return $num;
    }

    //批量修改物流单号
    public  function importChangeShipnum(){
        $file=request()->file('file');
        $info=$file->move(ROOT_PATH.'public'.DS.'uploads');
        if($info){
            if($info->getExtension()!="csv")
            {
                return AjaxReturnMsg("表格格式错误，只支持CSV");
            }
            $path=$info->getPathname();
            \think\Log::record("path:".$path,'zyx');
            $handle=fopen($path,'r');
            $count=0;
            $order_count=0;
            Db::startTrans();
            try {
                while ($row = fgetcsv($handle)) {
                    if($row[0]==null){
                        \think\Log::record("row empty".var_export($row,true),'zyx');
                        //return AjaxReturnMsg("表格中有空行，请检查");
                        $count++;
                        continue;
                    }
                    if($order_count>=9999)
                    {
                        return AjaxReturnMsg("超过单次上传上限9999条");
                    }
                    if ($count > 0) {//第一行忽略
                        $data=Array();
                        $startIndex=0;
                        $id=trim(getUtf8($row[$startIndex++]));
                        $sellinfo=$this->model->where(['id'=>$id])->find();
                        //if($sellinfo["status"])
                        if(empty($sellinfo))
                        {
                            $msg=$this->getRowInfo($count,$startIndex)."订单找不到".$id;
                            return AjaxReturnMsg($msg);
                        }
                        $shipnum=trim(getUtf8($row[$startIndex++]));
                        $data["logistics"]=$shipnum;
                        $this->model->updateOne($id,$data);
                        $this->logModel->addLog("订单:".$id." 源物流单号:".$sellinfo["logistics"]." 修改内容：".json_encode($data,JSON_UNESCAPED_UNICODE));
                    }
                    $count++;
                }
                DB::commit();
                $url_path=str_replace(ROOT_PATH,'',$info->getPathname());
                $this->logModel->addLog(sprintf("批量修改成功"),$url_path,'info');
                return AjaxReturn(SUCCESS);
            }
            catch (\Exception $e){
                \think\Log::record("import data err:".$e->getMessage(),'zyx');
                DB::rollback();
                return AjaxReturnMsg("批量修改失败:".$e->getMessage());
            }
        }
        else
        {
            \think\Log::record("err:".$info->getError(),'zyx');
            return AjaxReturn(UPLOAD_ERROR);
        }
    }
    //csv导入
    public  function  importData()
    {
        \think\Log::record("importData",'zyx');
        $file=request()->file('file');

        $info=$file->move(ROOT_PATH.'public'.DS.'uploads');
        if($info){
            if($info->getExtension()!="csv")
            {
                return AjaxReturnMsg("表格格式错误，只支持CSV");
            }
            $path=$info->getPathname();
            \think\Log::record("path:".$path,'zyx');
            $handle=fopen($path,'r');
            $count=0;
            $order_count=0;
            Db::startTrans();
            try {
                //$before_num=$this->getTotalNum(['status'=>CHECK_NO]);
                while ($row = fgetcsv($handle)) {
                    if($row[0]==null){
                        \think\Log::record("row empty".var_export($row,true),'zyx');
                        //return AjaxReturnMsg("表格中有空行，请检查");
                        $count++;
                        continue;
                    }
                    if($order_count>=9999)
                    {
                        return AjaxReturnMsg("超过单次上传上限9999条");
                    }
                    if ($count > 0) {//第一行忽略
                        $msg ="";
                        $ret = $this->insertRow($row, $msg,$order_count,$count);
                        if ($ret != SUCCESS) {
                            if($msg!="")
                            {
                                return AjaxReturnMsg($msg);
                            }
                            else {
                                return AjaxReturn($ret);
                            }
                        }
                    }
                    $count++;
                }
                DB::commit();
                $url_path=str_replace(ROOT_PATH,'',$info->getPathname());
                //$after_num=$this->getTotalNum(['status'=>CHECK_NO]);
                //$this->logModel->addLog(sprintf("导入前待审核商品罐数:%d 导入后:%d",$before_num,$after_num),$url_path,'info');
                //$this->logModel->addLog(sprintf("导入前待审核商品罐数:%d 导入后:%d"),$url_path,'info');
                return AjaxReturn(SUCCESS);
            }
            catch (\Exception $e){
                \think\Log::record("import data err:".$e->getMessage(),'zyx');
                DB::rollback();
                return AjaxReturnMsg("导入失败:".$e->getMessage());
            }
        }
        else
        {
            \think\Log::record("err:".$info->getError(),'zyx');
            return AjaxReturn(UPLOAD_ERROR);
        }

    }

    //同步物流单号
    public  function  syncOrderShipNum(){
        $shopId= request()->post('shop_id', null);
        if(empty($shopId)){
            // return AjaxReturnMsg("目标商城不能为空");
            $synctarget="logistics";
        }
        else{
            $synctarget="shop";
            $shopInfo=$this->shopModel->where(['id'=>$shopId])->find();
            if(empty($shopInfo))
            {
                return AjaxReturnMsg("商店不存在");
            }
            $shopurl=$shopInfo["shop_edit_url"];
        }
    
        $orderarr= request()->post('id/a', null);
        // $synctarget= request()->post('target', null);
        $idlist=array();
        $okshipnum=array();
        $searcharr= request()->post('search/a', null);

        if(empty($searcharr)){
            //return AjaxReturnMsg("订单空");
            if(empty($orderarr)){
                return AjaxReturnMsg("订单空");
            }
        }else{
            $totalnum=$this->model->preAll($searcharr,false,null)->where($searcharr)->count();
            if($totalnum>1000){
                return AjaxReturnMsg("单次同步数量超过上限");
            }
            if($totalnum==0){
                return AjaxReturnMsg("无订单");
            }
        }

        if(count($orderarr)>1000){
            return AjaxReturnMsg("单次同步数量超过上限");
        }

        //$oksellids=array();
        if(empty($searcharr)){
            \think\Log::record("get ids",'zyx');
            foreach ($orderarr as $sellid){
                $sellinfo=$this->model->where(['id'=>$sellid])->find();
                if(empty($sellinfo))
                {
                    return AjaxReturnMsg("订单找不到".$sellid);
                }
                $res=$this->initOneSyncOrder($sellinfo,$shopId,$idlist,$okshipnum);
                if(empty($res)==false){
                    return AjaxReturnMsg($res);
                }

            }
        }else{
            \think\Log::record("get all:".var_export($searcharr,true),'zyx');
            $alllist=$this->model->preAll($searcharr,true,null)->where($searcharr)->select();
            foreach ($alllist as $sellinfo){
                $res=$this->initOneSyncOrder($sellinfo,$shopId,$idlist,$okshipnum);
                if(empty($res)==false){
                    return AjaxReturnMsg($res);
                }
            }
        }
        if(empty($idlist)){
            return AjaxReturnMsg("没有符合条件的可同步订单");
        }

        if($synctarget=="shop"){
            $url=$shopurl;
            $senddata=array();
            $senddata["list"]=$idlist;
            \think\Log::record("senddata before:".var_export($senddata,true),'zyx');
            $senddatastr = json_encode($senddata);
            $opts = array(
                "http"=>array(
                    'method'=>'POST',
                    'header'=>"Content-type: application/x-www-form-urlencodedrn",
                    "Content-Length:"=> strlen($senddatastr)."",
                    "content"=>$senddatastr
                )
            );
            try{
                $context=stream_context_create($opts);
                \think\Log::record("send url:".$url,'zyx');
                \think\Log::record("senddata:".var_export($senddata,true),'zyx');
                $res=file_get_contents($url,false,$context);
                $arrres=json_decode($res,true);
                \think\Log::record("get res ".$res,'zyx');
                if($arrres["code"]==1){
                    
                    $this->logModel->addLog(sprintf("同步订单，订单列表:%s 发送的同步数据：%s",var_export($orderarr,true),var_export($senddata,true)),"",'info');
                    //return AjaxReturn(SUCCESS,$idlist);
                }else{
                    return AjaxReturnMsg($arrres["message"]);
                }
            }
            catch (\Exception $e){
                \think\Log::record("send data err:".$e->getMessage(),'zyx');
            
                return AjaxReturnMsg("同步失败:".$e->getMessage());
            }
        }
       


        //同步到物流
        $res=$this->syncLogicToLogic($okshipnum);
        if(empty($res)){
            //return AjaxReturn(SUCCESS);
            //更新状态
            Db::startTrans();
            try {
                foreach ($idlist as $shoporder=>$value){
                    $this->model->where(['shop_order'=>$shoporder])->update(["shop_sync_flag"=>1]);
                }
                DB::commit();
            }
            catch (\Exception $e){
                DB::rollback();
                return AjaxReturnMsg($e->getMessage());
            }
        }else{
            return AjaxReturnMsg($res);
        }
        $resdata=array();
        $resdata["shipnum"]=$okshipnum;
        $resdata["syncshop"]=$idlist;
        return AjaxReturn(SUCCESS,$resdata);
    }

    
    //同步到物流系统
    public function syncLogicToLogic($changelogistics){

        $senddata=array();
        $shipurl= $this->configModel->getConfig('logistics_url');
        if(empty($shipurl)==false){
            $senddata["list"]=$changelogistics;
            \think\Log::record("senddata before:".var_export($senddata,true),'zyx');
            $senddatastr = json_encode($senddata);
            $opts = array(
                "http"=>array(
                    'method'=>'POST',
                    'header'=>"Content-type: application/x-www-form-urlencodedrn",
                    "Content-Length:"=> strlen($senddatastr)."",
                    "content"=>$senddatastr
                )
            );
            $context=stream_context_create($opts);
            \think\Log::record("send url:".$shipurl,'zyx');
            \think\Log::record("senddata:".var_export($senddata,true),'zyx');
            $res=file_get_contents($shipurl,false,$context);
            $arrres=json_decode($res,true);
            \think\Log::record("get res ".$res,'zyx');
            if($arrres["code"]==1){
//                $this->logModel->addLog(sprintf("同步物流到物流系统，订单列表:%s",var_export($changelogistics,true)),"",'info');

            }else{
                return $arrres["message"];
            }
        }
        return null;
    }

    //初始化同步到商城的结构信息
     public function initOneSyncOrder($sellinfo,$shopId,&$idlist,&$okshipnum){

        $sellid=$sellinfo["id"];
        //代发仓的AB单号不同步的商城
      
        if($sellinfo['status']==DELETE_OK)
        {
            return "订单已经废弃".$sellid;
        }
        if(empty($shopId)==false){
            if($sellinfo["shop_id"]!=$shopId){
                return "订单商店不匹配".$sellid;
            }
        }else{
            $startpos=strpos($sellinfo["logistics"],"AB");
            if($sellinfo['store_id']==12&&($startpos===0)){
                \think\Log::record("store id=12 and AB id:".$sellinfo["logistics"]." res:".$startpos,'zyx');
                return;
            }
        }
    
        $shoporder=$sellinfo["shop_order"];
        $shipitemarr=array();
        $shipitemarr["logistics"]=$sellinfo["logistics"];
        $shipitemarr["Idnumpic1"]=$sellinfo["idnumpic1"];
        $shipitemarr["Idnumpic2"]=$sellinfo["idnumpic2"];
        $shipitemarr["customer_name"]=$sellinfo["customer_name"];
        $shipitemarr["user_id_number"]=$sellinfo["user_id_number"];
        $shipitemarr["client_phone"]=$sellinfo["user_phone"];
        $shipitemarr["client_address"]=$sellinfo["customer_addr"];
        $okshipnum[]=$shipitemarr;
        if(empty($idlist[$shoporder])){
            $shipnumlist=array();
            $orderlist=$this->model->where(["shop_order"=>$shoporder])->select();
            foreach ($orderlist as $orderitem){
                if($orderitem['status']==DELETE_OK)
                {
                    //return AjaxReturn("订单已经废弃".$sellid);
                }else{
                    $shipitemarrtemp=array();
                    $shipitemarrtemp["logistics"]=$orderitem["logistics"];
                    $shipnumlist[]=$shipitemarrtemp;
                }
            }
            //$idlist[$shoporder]=json_encode($shipnumlist);
            $idlist[$shoporder]=$shipnumlist;
        }
        return null;
    }

    //物流系统同步物流信息到erp
    public  function  syncLogicstics()
    {
        $token = request()->post('token', null);
        if (empty($token)) {
            return AjaxReturnMsg("无权限");
        }
        $shopid = request()->post('shop_id', null);
        $shopinfo = $this->shopModel->where(['id' => $shopid])->find();
        if (empty($shopinfo)) {
            return AjaxReturnMsg("商店不存在");
        }
        if ($shopinfo["token"] != $token) {
            return AjaxReturnMsg("token错误");
        }

        $datalist = request()->post('list/a', null);
        if(empty($datalist)==true){
            return AjaxReturnMsg("数据空");
        }
        Db::startTrans();
        try{
            foreach ($datalist as $dataitem){
                $shipid=$dataitem["id"];
                $sellinfo=$this->model->where(["logistics"=>$shipid])->find();
                if(empty($sellinfo)){
                    return AjaxReturnMsg("物流不存在");
                }
                $newdata=array();
                if(empty($dataitem["idnumpic1"])==false){
                    $newdata["idnumpic1"]=$dataitem["idnumpic1"];
                }
                if(empty($dataitem["idnumpic2"])==false){
                    $newdata["idnumpic2"]=$dataitem["idnumpic2"];
                }
                if(empty($dataitem["idnum"])==false){
                    $newdata["user_id_number"]=$dataitem["idnum"];
                }
                $ret=$this->model->where(['logistics'=>$shipid])->update($newdata);
            }
            DB::commit();
            $logdata=array();
            $logdata['controller']=$this->control;
            $logdata['method']=$this->action;
            $logdata['time']=time();
            $logdata['info']='[info]'.sprintf("商店:%s 修改物流身份证%s",$shopinfo["name"],var_export($datalist,true));
            $logdata['userid']=$this->uid;
            $this->logModel->AddLogData($logdata);
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("import data err:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg("导入失败:".$e->getMessage());
        }
    }

    function  GetLogisticsData($sellinfo){
        $shipitem=array();
        $shipitem["Logistics"]=$sellinfo["logistics"];
        $shipitem["Idnumpic1"]=$sellinfo["idnumpic1"];
        $shipitem["Idnumpic2"]=$sellinfo["idnumpic2"];
        $shipitem["customer_name"]=$sellinfo["customer_name"];
        $shipitem["user_id_number"]=$sellinfo["user_id_number"];
        $shipitem["client_address"]=$sellinfo["customer_addr"];
        $shipitem["client_phone"]=$sellinfo["user_phone"];
        return $shipitem;
    }
    //从商城导入订单到erp
    public  function  addOneOrder(){
        Debug::remark('addOneOrder1', 'time');
        $orderNum=1;
        $token= request()->post('token', null);
        if(empty($token)){
            return AjaxReturnMsg("无权限");
        }
        $shopid= request()->post('shop_id', null);
        $sendflag= request()->post('sendflag', 1);
        $shopinfo=$this->shopModel->where(['id'=>$shopid])->find();
        if(empty($shopinfo))
        {
            return AjaxReturnMsg("商店不存在");
        }
        if($shopinfo["token"]!=$token){
            return AjaxReturnMsg("token错误");
        }
        $importarr= request()->post('data/a', null);

        $newShipnumarr=array();
        Db::startTrans();
        Debug::remark('addOneOrder2', 'time');
        try{
            $getData=Array();
            foreach ($importarr as $key=>$importdata)
            {
                $data=array();
                $data['shop_order']=$importdata["shop_order"];
                $data['shop_id']=$shopid;
                $data['pay_time']=$importdata["pay_time"];
                $data['customer_account']=$importdata["customer_account"];
                $itemcode=$importdata['item_code'];
                $itemInfo=$this->itemModel->where(['code'=>$itemcode])->find();
                if(empty($itemInfo))
                {
                    return AjaxReturnMsg("订单:".$data['shop_order']."商品不存在:".$itemcode);
                }
                \think\Log::record("oneorder ",'zyx');
                Debug::remark('addOneOrdertemp0', 'time');
                $data['item_id']=$itemInfo["id"];
                $itemValueBase=$itemInfo['sell_base_num'];//商品的最小约数
                $data['num']=$importdata["num"];
                $totalprice=$importdata["total_price"];

                $data['pay_money']=$totalprice;
                $data['discount']=1;
                $data['freight_price']=$importdata["freight_price"];
                $data['service_price']=$importdata["service_price"];
                $data['supply_source']=$importdata["supply_source"];
                $data['idnumpic1']=$importdata["idnumpic1"];
                $data['track_man']=$importdata["track_man"];
                $data['idnumpic2']=$importdata["idnumpic2"];
                $data['unit_price']=(intval($totalprice)-intval($data['freight_price'])-intval($data['service_price']))/intval($data['num']);
                $data['pay_id']=$importdata["pay_id"];
                $data['customer_username']=$importdata["customer_username"];
                $data['customer_userid']=$importdata["customer_userid"];
                $data['order_time']=$importdata["order_time"];
                $data['freight_unit_price']=(float)$data['freight_price']/ (float)$data['num'];
                $data['service_unit_price']=(float)$data['service_price']/(float) $data['num'];
                //买家姓名
                $data['customer_name']=$importdata["customer_name"];
                $data['send_user_name']=$importdata["send_user_name"];
                $data['send_user_phone']=$importdata["send_user_phone"];
                $data['user_phone']=$importdata["client_phone"];
                $data['customer_addr']=$importdata["customer_addr"];
                $data['customer_province']=$importdata["customer_province"];
                $data['customer_city']=$importdata["customer_city"];
                $data['customer_area']=$importdata["customer_area"];
                $data['user_id_number']=$importdata["user_id_number"];
                $data['user_info']=$importdata["user_info"];
                $data['sell_vip_type']=$importdata["sell_vip_type"];
                $data['pay_type']=$importdata["pay_type"];
                $data['pay_check_info']=$importdata["pay_check_info"];
                $vipType=SELL_VIP_NOMRAL;
                if(!empty($data['sell_vip_type']))
                {
                    $vipType=intval($data['sell_vip_type']);
                    if($vipType>SELL_VIP_MAX||$vipType<SELL_VIP_MIN)
                    {
                        return AjaxReturnMsg("特殊要求标识不对");
                    }
                }
                $data['refund_status']=REFUND_NO;
                $data['build_time']=time();
                $data['build_user']=$this->uid;
                $data['pay_status']=PAY_OK;
                $data['status']=CHECK_NO;
                Debug::remark('addOneOrdertemp1', 'time');
                $ret=$this->model->where(['shop_order'=>$data['shop_order']])->where('status','<>',DELETE_OK)->find();
                Debug::remark('addOneOrdertemp2', 'time');
                if(!empty($ret)){
                    return AjaxReturnMsg("平台订单号重复");
                }
                $data['sell_type']=$importdata["sell_type"];
                if($data['sell_type']>SELL_TYPE_MAX||$data['sell_type']<SELL_TYPE_MIN)
                {
                    return AjaxReturnMsg("报关标识不对");
                }
                $sellTypeValue=1;
                if($data['sell_type']!=SELL_TYPE_NO)
                {
                    $sellTypeValue=$this->configModel->getSellBaseValue($data['sell_type']);
                }
                $msg="";
                Debug::remark('addOneOrdertemp3', 'time');
                //$before_num=$this->getTotalNum(['status'=>CHECK_NO]);
                Debug::remark('addOneOrdertemp4', 'time');
                $resItem=array();
                $resItem["id"]= $data['shop_order'];
                $resItem["logistics"]=array();

                Debug::remark('addOneOrder3', 'time');
                if($data['sell_type']!=SELL_TYPE_NO)
                {
                    $split_base=$itemValueBase>$sellTypeValue?$itemValueBase:$sellTypeValue;
                    $split_num=floor($data['num']/$split_base);
                    $split_remain=$importdata['num']%$split_base;
                    for ($index=0;$index<$split_num;$index++){

                        $ret=$this->importSplitOneOrder($split_base,$orderNum,$data,$msg);
                        if($ret!=SUCCESS){
                            return AjaxReturnMsg($msg);
                        }
                        array_push($resItem["logistics"],$data["logistics"]);
                        $newShipnumarr[]=$this->GetLogisticsData($data);

                    }
                    if($split_remain>0)
                    {
                        $ret=$this->importSplitOneOrder($split_remain,$orderNum,$data,$msg);
                        if($ret!=SUCCESS){
                            return AjaxReturnMsg($msg);
                        }
                        array_push($resItem["logistics"],$data["logistics"]);
                        $newShipnumarr[]=$this->GetLogisticsData($data);
                    }
                }
                else{
                    $ret=$this->importSplitOneOrder($data['num'],$orderNum,$data,$msg);
                    if($ret!=SUCCESS){
                        return AjaxReturnMsg($msg);
                    }
                    array_push($resItem["logistics"],$data["logistics"]);
                    $newShipnumarr[]=$this->GetLogisticsData($data);
                }
                Debug::remark('addOneOrder4', 'time');
                array_push($getData,$resItem);
            }
            //Debug::remark('addOneOrder5', 'time');
            //同步物流到物流系统
            if($sendflag==1){
                $res=$this->syncLogicToLogic($newShipnumarr);
                if(empty($res)){
                    //return AjaxReturn(SUCCESS);
                }else{
                    return AjaxReturnMsg($res);
                }
            }
            //Debug::remark('addOneOrder6', 'time');
            DB::commit();
            //$after_num=$this->getTotalNum(['status'=>CHECK_NO]);
            \think\Log::record("getData ".var_export($getData,true),'zyx');
            $logdata=array();
            $logdata['controller']=$this->control;
            $logdata['method']=$this->action;
            $logdata['time']=time();
            $logdata['info']='[info]'.sprintf("商店:%s 导入，",$shopinfo["name"]);
            $logdata['userid']=$this->uid;
            $this->logModel->insert($logdata);


            //Debug::remark('addOneOrder7', 'time');
//            \think\Log::record(' [ RunTime1:' . Debug::getRangeTime('addOneOrder1', 'addOneOrder2') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime2:' . Debug::getRangeTime('addOneOrder2', 'addOneOrder3') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime3:' . Debug::getRangeTime('addOneOrder3', 'addOneOrder4') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime4:' . Debug::getRangeTime('addOneOrder4', 'addOneOrder5') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime5:' . Debug::getRangeTime('addOneOrder5', 'addOneOrder6') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime6:' . Debug::getRangeTime('addOneOrder6', 'addOneOrder7') . 's ]', 'zyx');
//
//            \think\Log::record(' [ RunTime6:' . Debug::getRangeTime('addOneOrdertemp0', 'addOneOrdertemp1') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime6:' . Debug::getRangeTime('addOneOrdertemp1', 'addOneOrdertemp2') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime6:' . Debug::getRangeTime('addOneOrdertemp2', 'addOneOrdertemp3') . 's ]', 'zyx');
//            \think\Log::record(' [ RunTime6:' . Debug::getRangeTime('addOneOrdertemp3', 'addOneOrdertemp4') . 's ]', 'zyx');



            return AjaxReturn(SUCCESS,$getData);
        }
        catch (\Exception $e){
            \think\Log::record("import data err:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg("导入失败:".$e->getMessage());
        }
    }
    //导出
    public  function  exportCsv()
    {
        $search=request()->post('search/a', null); //标题名
        $headList=request()->post('headlist/a', ''); //标题名
        $filename=request()->post('filename', '');  //文件名
        $nameList=request()->post('namelist/a', ''); //字段名
        $type=request()->post('type', ''); //类型

        Session::set('search',$search);
        Session::set('headlist',$headList);
        Session::set('filename',$filename);
        Session::set('namelist',$nameList);
        Session::set('model','Sell');
        Session::set('type',$type);
        Session::set('modelName','data\model\SellModel');
        return AjaxReturn(SUCCESS);
    }

    private  function  getRowInfo($row,$col)
    {
        return sprintf("表格第%d行 第%d列出错:",$row+1,$col);
    }
    //初始化所选的未初始化的订单
    protected function insertRow($row,&$msg,&$orderNum,$rownum)
    {
        \think\Log::record("insertRow {$rownum} ".var_export($row,true),'zyx');
        $data=Array();
        $startIndex=0;
        $shop_name=trim(getUtf8($row[$startIndex++]));
        $shopinfo=$this->shopModel->where(['name'=>$shop_name])->find();
        if(empty($shopinfo))
        {
            $msg=$this->getRowInfo($rownum,$startIndex)."没有对应的商店名".$shop_name;
            return SYSTEM_ERROR;
        }

        $data['shop_id']=$shopinfo['id'];
        $data['shop_order']=trim($row[$startIndex++]);
        //获取订单时间
        $data['order_time']=trim($row[$startIndex++]);
        if(empty($data['order_time'])){
            $data['order_time']=time();
        }
        else{
          $date=date_create($data['order_time'],timezone_open('Asia/Shanghai'));
          if($date==false){
              $msg=$this->getRowInfo($rownum,$startIndex)."日期格式不对";
              return SYSTEM_ERROR;
          }

          $data['order_time']=$date->getTimestamp();
        }

        //获取付款时间
        $data['pay_time']=trim($row[$startIndex++]);
        if(empty($data['pay_time'])){
            $data['pay_time']=time();
        }
        else{
            $date=date_create($data['pay_time'],timezone_open('Asia/Shanghai'));
            if($date==false){
                $msg=$this->getRowInfo($rownum,$startIndex)."日期格式不对";
                return SYSTEM_ERROR;
            }
            $data['pay_time']=$date->getTimestamp();
        }

        //跟单员标识
        $data['track_man']=getUtf8(trim($row[$startIndex++]));
        //买家账号
        $data['customer_account']=getUtf8(trim($row[$startIndex++]));
        $data['send_user_name']=getUtf8(trim($row[$startIndex++]));
        $data['send_user_phone']=getUtf8(trim(trim($row[$startIndex++]),'#'));

       //商品名
        $itemname=getUtf8(trim($row[$startIndex++]));
        $itemInfo=$this->itemModel->where(['name'=>$itemname])->find();
        $itemValueBase=$itemInfo['sell_base_num'];//商品的最小约数
        if(empty($itemInfo))
        {
            $msg=$this->getRowInfo($rownum,$startIndex)."没有对应的商品名".$itemname;
            return SYSTEM_ERROR;
        }
        $data['item_id']=$itemInfo['id'];
        //数量
        $data['num']=intval($row[$startIndex++]);
        $data['unit_price']=trim($row[$startIndex++]);
        $data['discount']=$row[$startIndex++];
        $data['freight_price']=intval(trim($row[$startIndex++]));
        $data['service_price']=intval(trim($row[$startIndex++]));
        $data['freight_unit_price']=(float)$data['freight_price']/ (float)$data['num'];
        $data['service_unit_price']=(float)$data['service_price']/(float) $data['num'];

        //买家姓名
        $data['customer_name']=getUtf8(trim($row[$startIndex++]));
        $data['user_phone']=trim(trim($row[$startIndex++]),'#');
        $data['customer_addr']=getUtf8(trim($row[$startIndex++]));
        $data['customer_province']=getUtf8(trim($row[$startIndex++]));
        $data['customer_city']=getUtf8(trim($row[$startIndex++]));
        $data['customer_area']=getUtf8(trim($row[$startIndex++]));
        $data['user_id_number']=getUtf8(trim(trim($row[$startIndex++]),'#'));
        if(empty($data['user_id_number'])==false && checkIdCard($data['user_id_number'])==false){
            $msg=$this->getRowInfo($rownum,$startIndex).$data['user_id_number']."不是正确的身份证";
            return SYSTEM_ERROR;
        }
        $data['user_info']=getUtf8(trim($row[$startIndex++]));
        $data['sell_vip_type']=trim($row[$startIndex++]);
        if(!empty($data['sell_vip_type']))
        {
            $vipType=intval($data['sell_vip_type']);
            if($vipType>SELL_VIP_MAX||$vipType<SELL_VIP_MIN)
            {
                $msg=$this->getRowInfo($rownum,$startIndex).$data['shop_order']."特殊要求标识不对".$vipType;
                return SYSTEM_ERROR;
            }
        }

        $data['refund_status']=trim($row[$startIndex++]);
        if(!empty($data['refund_status']))
        {
            $refund=intval($data['refund_status']);
            if($refund>REFUND_MAX||$refund<REFUND_MIN)
            {
                $msg=$this->getRowInfo($rownum,$startIndex).$data['shop_order']."退款状态不对".$refund;
                return SYSTEM_ERROR;
            }
        }
        else{
            $data['refund_status']=REFUND_MIN;
        }

        $data['sell_type']=strtoupper(trim($row[$startIndex]));
        if(!empty($data['sell_type']))
        {
            if($data['sell_type']=="CC"){
                $data['sell_type']=SELL_TYPE_CC;
            }
            else if($data['sell_type']=="BC"){
                $data['sell_type']=SELL_TYPE_BC;
            }
            else if($data['sell_type']=="NS"){
                $data['sell_type']=SELL_TYPE_NS;
            }
            else if($data['sell_type']=="NO"){
                $data['sell_type']=SELL_TYPE_NO;
            }
            else{
                $msg=$this->getRowInfo($rownum,$startIndex).$data['shop_order']."报关标识不对".$data['sell_type'];
                return SYSTEM_ERROR;
            }
        }
        if($data['sell_type']!=SELL_TYPE_NO)
        {
            $sellTypeValue=$this->configModel->getSellBaseValue($data['sell_type']);
        }


        //支付金额
        $data['check_user']=$this->configModel->getConfig("sell_check_default_user");
        $data['build_time']=time();
        $data['build_user']=$this->uid;
        $data['pay_status']=PAY_OK;
        $data['pay_status']=PAY_OK;
        $data['status']=CHECK_NO;
        //\think\Log::record("check3 {$rownum} {$startIndex}",'zyx');
        $ret=$this->model->where(['shop_order'=>$data['shop_order']])->find();
        if(!empty($ret)){
            $msg=$this->getRowInfo($rownum,$startIndex).$data['shop_order']."平台订单号重复";
            //\think\Log::record("check6 {$msg} ",'zyx');
            return SYSTEM_ERROR;
        }
        //\think\Log::record("check4",'zyx');
        //拆单
        //\think\Log::record("begin split {$itemValueBase} {$sellTypeValue}",'zyx');
        if($data['sell_type']!=SELL_TYPE_NO)
        {
            $split_base=$itemValueBase>$sellTypeValue?$itemValueBase:$sellTypeValue;
            $split_num=floor($data['num']/$split_base);
            $split_remain=$data['num']%$split_base;
            for ($index=0;$index<$split_num;$index++){
                $ret=$this->importSplitOneOrder($split_base,$orderNum,$data,$msg);
                if($ret!=SUCCESS){
                    return $ret;
                }
            }
            if($split_remain>0)
            {
                $ret=$this->importSplitOneOrder($split_remain,$orderNum,$data,$msg);
                if($ret!=SUCCESS){
                    return $ret;
                }
            }
        }
        else{
            $ret=$this->importSplitOneOrder($data['num'],$orderNum,$data,$msg);
            if($ret!=SUCCESS){
                return $ret;
            }
        }

        return SUCCESS;

    }

    public function  RefreshLogistics(){
        $sellid = request()->post('id', null);
        if(empty($sellid)==true){
            return AjaxReturnMsg("id空");
        }
        $logistics_num=$this->addNewLogistics();
        if($logistics_num==SYSTEM_ERROR){
            return AjaxReturnMsg("失败");
        }
        $ret=$this->model->where(["id"=>$sellid])->update(["logistics"=>$logistics_num]);
        if(empty($ret))
        {
           
            return AjaxReturnMsg("失败");
        }
        return AjaxReturn(SUCCESS);
    }

    private function addNewLogistics(){
        $ret=$this->configModel->where('name','=','logistics_count')->setInc('int_value',1);
        if(empty($ret))
        {
            $msg=$data['shop_order']."数据库物流单号操作异常";
            return SYSTEM_ERROR;
        }
        $logisticsCountConf=$this->configModel->lock(true)->where('name','=','logistics_count')->find();
        $logisticsBase=$this->configModel->getConfig("logistics_base");

        preg_match('/^([^\d]*)([\d]*)([^\d]*)$/',$logisticsBase,$match);
        //\think\Log::record("base data:".$logisticsBase,'zyx');
        //\think\Log::record("match data:".var_export($match,true),'zyx');
        $logisticsValue=intval($match[2]);
        if(empty($logisticsValue))
        {
            $msg=$data['shop_order']."数据库物流单号异常";
            return SYSTEM_ERROR;
        }
        return $match[1].($logisticsValue+$logisticsCountConf['int_value']).$match[3];
    }

    //拆分一个订单
    private  function  importSplitOneOrder($itemnum,&$orderNum,&$data,&$msg)
    {
       $logistics_num=$this->addNewLogistics();
       if($logistics_num==SYSTEM_ERROR){
           return SYSTEM_ERROR;
       }
        $orderNum=$orderNum+1;
        $data['id']=getOrderId(SELL_PRE,$this->uid,$orderNum);
        $data['logistics']=$logistics_num;
        $data['num']=$itemnum;

        $data['pay_money']=floatval($data['unit_price'])*$data['num']+$data['freight_unit_price']*$data['num']+$data['service_unit_price']*$data['num'];
//        \think\Log::record("insert data:".var_export($data,true),'zyx');
        $ret=$this->model->insert($data);
        if(empty($ret))
        {
            return INSERT_ERROR;
        }
        $this->logModel->addLog(sprintf("增加单:%s  logistics:%s",$data["id"],$data["logistics"]));
        return SUCCESS;
    }

    public  function splitOrder()
    {
        $id= request()->post('id', null);
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        Db::startTrans();
        try{
            $before_num=$this->getTotalNum(['status'=>CHECK_NO]);
            $sellInfo=$this->model->where(['id'=>$id])->find();
            if(empty($sellInfo)){
                return AjaxReturnMsg("订单不存在");
            }
            if(empty($sellInfo['logistics_merge'])){
                return AjaxReturnMsg("没有合过的订单不能拆");
            }
            if(empty($sellInfo['merge_order'])){
                return AjaxReturnMsg("没有合过的订单不能拆");
            }
            $ret=$this->splitOneOrder($sellInfo,$msg);
            if($ret!=SUCCESS){
                return AjaxReturnMsg($msg);
            }
            DB::commit();
            $after_num=$this->getTotalNum(['status'=>CHECK_NO]);
            //$this->logModel->addLog(":".$id);
            $this->logModel->addLog(sprintf("拆单id:%s 拆单前待审核商品罐数:%d 拆单后:%d",$id,$before_num,$after_num));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("mergeOrder:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }

    public  function splitAllOrder()
    {
        Db::startTrans();
        try{
            $before_num=$this->getTotalNum(['status'=>CHECK_NO]);
            $sellList=$this->model->where(["status"=>CHECK_NO])->where('logistics_merge','<>','')->select();
            if(empty($sellList)){
                return AjaxReturnMsg("没有可以拆的订单");
            }
            foreach ($sellList as $key=>$value) {
                $ret=$this->splitOneOrder($value,$msg);
                if($ret!=SUCCESS){
                    return AjaxReturnMsg($msg);
                }
            }
            $after_num=$this->getTotalNum(['status'=>CHECK_NO]);
            DB::commit();
            //$this->logModel->addLog("拆订单:".json_encode($sellList,JSON_UNESCAPED_UNICODE));

            $this->logModel->addLog(sprintf("拆单前待审核商品罐数:%d 拆单后:%d"
                ,$before_num,$after_num));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("mergeOrder:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }

    public  function  splitOneOrder($orderinfo,&$msg)
    {
        if($orderinfo['num']!=SPLITE_SELL_BASE*2){
            $msg="该单数量有误";
            return SYSTEM_ERROR;
        }
        if($orderinfo['status']!=CHECK_NO){
            $msg="只有待审核才能以拆单";
            return SYSTEM_ERROR;
        }
        if(empty($orderinfo['merge_order'])){
            $msg="合并单号空";
            return SYSTEM_ERROR;
        }
        $newData=array();
        $idNew=$orderinfo['merge_order'];
        $splitinfo=$this->model->where(["id"=>$idNew])->find();
        if(empty($splitinfo)){
            $msg="合并单号空错";
            return SYSTEM_ERROR;
        }

        $newData['status']=CHECK_NO;
        $newData['del_info']="";
//        \think\Log::record("newData:".var_export($newData,true),'zyx');
        $newnum=$orderinfo['num']-$splitinfo["num"];
        $ret=$this->model->updateOne($idNew,$newData);
        if(empty($ret)){
            $msg="更新错误".$orderinfo['id'];
            return SYSTEM_ERROR;
        }
        $freight_price=$orderinfo['freight_price'];
        $service_price=$orderinfo['service_price'];
        if(!empty($orderinfo['freight_unit_price'])){
            $freight_price=$newnum*$orderinfo['freight_unit_price'];
        }
        if(!empty($orderinfo['service_unit_price'])){
            $service_price=$newnum*$orderinfo['service_unit_price'];
        }

        $pay_money=floatval($orderinfo['unit_price'])*$newnum+$freight_price+$service_price;
        $changedata=['num'=>$newnum,'logistics_merge'=>'','pay_money'=>$pay_money];
        $ret=$this->model->updateOne($orderinfo['id'],$changedata);

        $this->logModel->addLog(sprintf("源单id:%s 拆单后新增id:%s",$orderinfo['id'],$idNew));
        if(empty($ret)){
            $msg="更新错误".$orderinfo['id'];
            return SYSTEM_ERROR;
        }
        return SUCCESS;
    }

    //合并两个订单
    public  function  mergeOrder()
    {
        $id= request()->post('id/a', null);
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        if(count($id)!=2)
        {
            return AjaxReturn(SELL_MERGE_MORETWO_ERROR);
        }

        Db::startTrans();
        try{
            $before_num=$this->getTotalNum(['status'=>CHECK_NO]);
            $ret=$this->mergeTwoOrder($id[0],$id[1],$msg);
            if($ret!=SUCCESS){
                return AjaxReturnMsg($msg);
            }
            DB::commit();
            $after_num=$this->getTotalNum(['status'=>CHECK_NO]);
            //$this->logModel->addLog("合并订单:".json_encode($id));
            $this->logModel->addLog(sprintf("合并订单:%s 合单前待审核商品罐数:%d 合单后:%d"
                ,json_encode($id),$before_num,$after_num));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("mergeOrder:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }

    //批量合并
    public function mergeAllOrder(){
        $id= request()->post('id/a', null);
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }

        Db::startTrans();
        try{
            $before_num=$this->getTotalNum(['status'=>CHECK_NO]);
            foreach ($id as $key=>$value){
                if(count($value)!=2)
                {
                    return AjaxReturn(SELL_MERGE_MORETWO_ERROR);
                }
                $ret=$this->mergeTwoOrder($value[0],$value[1],$msg);
                if($ret!=SUCCESS){
                    return AjaxReturnMsg($msg);
                }
            }
            $after_num=$this->getTotalNum(['status'=>CHECK_NO]);
            DB::commit();
            //$this->logModel->addLog("合并订单:".json_encode($id));
            $this->logModel->addLog(sprintf("合并订单:%s 合单前待审核商品罐数:%d 合单后:%d"
                ,json_encode($id),$before_num,$after_num));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("mergeOrder:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }

    private  function  mergeTwoOrder($id1,$id2,&$errMsg)
    {
        $sellInfo1=$this->model->where(['id'=>$id1])->find();
        $sellInfo2=$this->model->where(['id'=>$id2])->find();
        if(empty($sellInfo1))
        {
            $errMsg=$id1."订单找不到";
            return SYSTEM_ERROR;
        }
        if($sellInfo1['status']!=CHECK_NO){
            $errMsg="只有待审核才能以合单";
            return SYSTEM_ERROR;
        }
        if($sellInfo2['status']!=CHECK_NO){
            $errMsg="只有待审核才能以合单";
            return SYSTEM_ERROR;
        }
        if(empty($sellInfo2))
        {
            $errMsg=$id2."订单找不到";
            return SYSTEM_ERROR;
        }
        if($sellInfo1['shop_order']!=$sellInfo2['shop_order'])
        {
            $errMsg=$id1."和".$id2."商店订单号不一致";
            return SYSTEM_ERROR;
        }
        if($sellInfo1['num']!=SPLITE_SELL_BASE){
            $errMsg=$id1."数量不符合要求";
            return SYSTEM_ERROR;
        }
        if($sellInfo2['num']!=SPLITE_SELL_BASE){
            $errMsg=$id2."数量不符合要求";
            return SYSTEM_ERROR;
        }

        $totalNum=$sellInfo1['num']+$sellInfo2['num'];
        $freight_price=$sellInfo1['freight_price'];
        $service_price=$sellInfo1['service_price'];
        if(!empty($sellInfo1['freight_unit_price'])){
            $freight_price=$totalNum*$sellInfo1['freight_unit_price'];
        }
        if(!empty($sellInfo1['service_unit_price'])){
            $service_price=$totalNum*$sellInfo1['service_unit_price'];
        }

        $pay_money=floatval($sellInfo1['unit_price'])*$totalNum+$freight_price+$service_price;
        if(SUCCESS!=$this->model->updateOne($id1,["num"=>$totalNum,'merge_order'=>$id2,
                'logistics_merge'=>$sellInfo2['logistics'],'pay_money'=>$pay_money]))
        {
            $errMsg=$id1."和".$id2."合并出错";
            return SYSTEM_ERROR;
        }

        $ret =$this->model->updateOne($id2,['status'=>DELETE_OK,'del_info'=>"合并订单，合并后订单：".$id1]);

        if($ret!=SUCCESS){
            $errMsg="删除多余订单错误:".$id2;
            return SYSTEM_ERROR;
        }
        return SUCCESS;
    }


    //作废
    public function del()
    {
        $id= request()->post('id/a', '');
        $del_info=request()->post('del_info', '');
        if(empty($del_info)){
            return AjaxReturn(SELL_DEL_EMPTY);
        }
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        Db::startTrans();
        try{
            foreach ($id as $key => $val) {
                $sellinfo=$this->model->where(['id'=>$val])->lock(true)->find();
                if(empty($sellinfo))
                {
                    return AjaxReturn(ID_ERROR);
                }

                if($sellinfo['status']==ASSIGN_OK){
                    //已经配货的
                    //仓库商品对应增加
                    $this->inStoreLogModel->addOneLog(SELL_DEL_TYPE,$val,$sellinfo['store_id'],$sellinfo['item_id'],$sellinfo['num'],true);
                    $ret=$this->storeItemModel->addItem($sellinfo['item_id'],$sellinfo['num'],$sellinfo['store_id'],'in_store');
                    if($ret!=SUCCESS){
                        return AjaxReturn($ret);
                    }
                }
                if($sellinfo['status']!=ASSIGN_OK&&$sellinfo['status']!=CHECK_NO){
                    return AjaxReturnMsg("非已配货或者待审核状态的订单不能直接废弃");
                }

                if(SUCCESS!=$this->model->updateOne($val,["status"=>DELETE_OK,'del_info'=>$del_info]))
                {
                    return AjaxReturn(SYSTEM_ERROR);
                }



            }
            DB::commit();
            $this->logModel->addLog("订单:".json_encode($id));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("initData:".$e->getMessage()." lines:".var_export($e->getTrace(),true),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }
    }

    private  function  getFieldRule($itemid,$orderRules)
    {
        //遍历所有的规则
        foreach ($orderRules as $rule){
            $orderList=$rule['orders'];
            foreach ($orderList as $order){
                if($order['itemid']==$itemid){
                    return $rule;
                }
            }
        }
        return null;
    }

    //审核
    public function  checkOk()
    {
        $id= request()->post('id/a', null);
        $storeId= request()->post('store_id', null);
        if(empty($id)){
            return AjaxReturn(ID_EMPTY);
        }
        if(empty($storeId)){
            return AjaxReturn(SELL_STORE_EMPTY);
        }
        $storeConfig=$this->configModel->getStoreConfig($storeId);
        $order_num_limit=$storeConfig['sell_num_limit'];
        $storeName=$storeConfig['name'];
        $order_field_check=$storeConfig['check_rule'];

        \think\Log::record("仓库要求".var_export($storeConfig,true),'zyx');
        Db::startTrans();
        try{
            foreach ($id as $key => $val) {
                $sellInfo=$this->model->where(['id'=>$val])->lock(true)->find();
                if(empty($sellInfo))
                {
                    return AjaxReturn(ID_ERROR);
                }
                if($sellInfo['status']!=CHECK_NO)
                {
                    return AjaxReturnMsg(sprintf("订单:%s 状态错误:%d",$val,$sellInfo['status']));
                }
//                if($sellInfo['check_user']!=$this->uid)
//                {
//                    return AjaxReturnMsg(sprintf("订单:%s 审核人不对",$sellInfo['id']));
//                }
                if(!empty($order_num_limit)){
                    if($sellInfo['num']!=$order_num_limit){
                        return AjaxReturnMsg(sprintf("<<%s>>要示订单商品数量为：%d,审核订单<<%s>>不满足需求",$storeName,$order_num_limit,$val));
                    }
                }
                //先审核
                if(SUCCESS!=$this->model->updateOne($val,["status"=>CHECK_OK,"check_time"=>time(),'store_id'=>$storeId]))
                {
                    return AjaxReturn(SYSTEM_ERROR);
                }
                //已卖
                $this->storeItemModel->addItem( $sellInfo['item_id'],$sellInfo['num'],$storeId,'in_sale');
                if(!empty($order_field_check))
                {
                    $fieldList=$order_field_check['fields'];
                    if(!empty($fieldList)&&!empty($order_field_check['rules'])) {
                        $searchArr = array();
                        $searchArr['status'] = CHECK_OK;
                        $searchArr['store_id'] = $storeId;
                        $haveNum = 0;//计算这个人在所有的订单组合中已经有的订单数
                        $haveOrders=null;
                        foreach ($fieldList as $field) {
                            $keyName = $field['name'];
                            $value = $sellInfo[$keyName];
                            $searchArr[$keyName] = $value;
                            $haveOrders = $this->model->where($searchArr)->where(function ($query) use ($order_field_check) {
                                foreach ($order_field_check['rules'] as $rule) {
                                    foreach ($rule['orders'] as $order) {
                                        $query->whereOr(function ($itemQuery) use ($order) {
                                            $itemQuery->where(['item_id' => $order['itemid']])->where(['num' => $order['num']]);
                                        });
                                    }
                                }
                            })->select();
                            if (count($haveOrders) > $haveNum) {
                                $haveNum = count($haveOrders);
                            }
                        }
                        if(!empty($haveOrders))
                        {
                            $limit_num = 0;//这个人限制数量
                            foreach ($haveOrders as $order)
                            {
                                $rule=$this->getFieldRule($order['item_id'],$order_field_check['rules']);
                                if(!empty($rule)){
                                    if($limit_num==0||$limit_num>$rule['num']){
                                        $limit_num=$rule['num'];
                                    }
                                }
                            }

                            if($haveNum>$limit_num)
                            {
                                return AjaxReturnMsg(sprintf("订单<<%s>>审核后，该用户过审数量达到%d 超过规则上限:%d 因此不能过审",
                                    $val,$haveNum,$limit_num));
                            }
                        }

                    }
                }

            }
            DB::commit();
            $this->logModel->addLog("仓库:".$storeId."订单:".json_encode($id));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("checkok:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }

   
    //反审核
    public function  checkNo()
    {
        $id= request()->post('id/a', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }

        Db::startTrans();
        try{
            foreach ($id as $key => $val) {
                $info=$this->model->where(['id'=>$val])->lock(true)->find();
                if(empty($info))
                {
                    return AjaxReturn(ERROR_ID);
                }
                if($info['status']==DELETE_OK)
                {
                    return AjaxReturn(CHECK_CLOSE_ERROR);
                }
                if($info['status']!=CHECK_OK)
                {
                    return AjaxReturnMsg($val."状态不对");
                }
//                if($info['check_user']!=$this->uid)
//                {
//                    return AjaxReturn(CHECK_USER_ERROR);
//                }
                $this->storeItemModel->delItem( $info['item_id'],$info['num'],$info['store_id'],'in_sale');
                if(SUCCESS!=$this->model->updateOne($val,["status"=>CHECK_NO,"check_time"=>0,"store_id"=>0]))
                {
                    return AjaxReturn(SYSTEM_ERROR);
                }

            }
            DB::commit();
            $this->logModel->addLog("订单:".json_encode($id));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("checkNO:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }
    }


    //修改
    public  function  edit()
    {
        $data=array();
        $id=request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $oldInfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldInfo))
        {
            return AjaxReturnMsg("无此订单信息");
        }

        $checkRes=$this->checkData($data,$oldInfo);
        \think\Log::record("data:".var_export($data,true));
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        if(!empty($data['track_man'])){
            $ret=$this->checkAuth($this->control,"updateTrackMan");
            if($ret!=SUCCESS){
                return AjaxReturn($ret);
            }
        }
        $ret=$this->model->updateOne($id,$data);
        if($ret!=SUCCESS){
            return AjaxReturn($ret);
        }
        $this->logModel->addLog("订单:".$id." 修改内容：".json_encode($data,JSON_UNESCAPED_UNICODE));
        return AjaxReturn($ret);
    }
}