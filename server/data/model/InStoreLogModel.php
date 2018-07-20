<?php
/**
 * Created by zyx.
 * Date: 2018-3-29
 * Time: 17:36
 */

namespace data\model;


class InStoreLogModel extends BaseModel
{
    protected $table="aq_instore_log";
    protected $rule=[];
    protected  $msg=[];

    protected $storeModel;
    protected $userModel;
    protected  $inStoreModel;
    protected  $outStoreModel;
    protected  $changeStoreModel;
    protected  $storeItemModel;
    protected  $itemModel;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storeModel=new StoreModel();
        $this->userModel=new UserModel();
        $this->inStoreModel=new BuyInStoreModel();
        $this->outStoreModel=new BuyOutModel();
        $this->changeStoreModel=new StoreChangeModel();
        $this->storeItemModel=new StoreItemModel();
        $this->itemModel=new ItemModel();


    }


    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $store=$this->storeModel->getTable();
        $user=$this->userModel->getTable();
        $item=$this->itemModel->getTable();


        return $this->alias('instore_log')
            ->join([$store=>"store"],'instore_log.store_id=store.id','left')
            ->join([$user=>"user"],'instore_log.user_id=user.id','left')
            ->join([$item=>"item"],'instore_log.item_id=item.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'instore_log.id'=>"id",
            'instore_log.type'=>"type",
            'instore_log.time'=>"time",
            'store.name'=>"store_name",
            'instore_log.store_id'=>"store_id",
            'user.name'=>"user_name",
            'instore_log.order'=>"order",
            'instore_log.item_id'=>"item_id",
            'item.name'=>"item_name",
            'item.sort_id'=>"item_sort_id",
            'item.code'=>"item_code",
            'instore_log.before_num'=>"before_num",
            'instore_log.after_num-instore_log.before_num'=>"change_num",
            'instore_log.after_num'=>"after_num",
        );
    }


    //字段值特殊处理
    public  function  exportNameProcess($name,$data)
    {
        //Log::record("process:{$name} {$data}",'zyx');
        if($name=="type"){
            if($data===INSTORE_TYPE){
                return "入库";
            }
            else if($data===INSTORE_DEL_TYPE){
                return "入库单废弃";
            }
            else if($data===INSTORE_UPDATE_TYPE){
                return "入库单修改";
            }
            else if($data===OUTSTORE_TYPE){
                return "出库单";
            }
            else if($data===OUTSTORE_DEL_TYPE){
                return "出库单废弃";
            }
            else if($data===OUTSTORE_UPDATE_TYPE){
                return "出库单修改";
            }
            else if($data===CHANGESTORE_TYPE){
                return "调货单";
            }
            else if($data===CHANGESTORE_DEL_TYPE){
                return "调货单废弃";
            }
            else if($data===CHANGESTORE_UPDATE_TYPE){
                return "调货单修改";
            }
            else if($data===SELLOUT_TYPE){
                return "销售配货";
            }
            else if($data===SELLOUT_DEL_TYPE){
                return "销售配货废弃";
            }
            else if($data===SELL_DEL_TYPE){
                return "销售已配货直接废弃";
            }
        }
        else if($name=="time"){
            //Log::record("process time",'zyx');
            return date("y-m-d H:i:s",$data);
        }
        return $data;
    }

    public  function addOneLog($type,$order,$storeId,$itemid,$num,$addOrDel)
    {
        $data=array();
        $data["type"]=$type;
        $data["user_id"]=$this->uid;
        $data["time"]=time();
        $data["order"]=$order;
        $data["store_id"]=$storeId;
        $data["item_id"]=$itemid;
        $data["before_num"]=$this->storeItemModel->getItemNum($itemid,$storeId,'in_store');
        $storeInfo=$this->storeModel->where(['id'=>$storeId])->find();

        if($addOrDel==true){
            $data["after_num"]=$data["before_num"]+$num;
        }
        else{
            $data["after_num"]=$data["before_num"]-$num;
        }
        $this->insert($data);
        if(!empty($storeInfo['same_store']))
        {
            $data["store_id"]=$storeInfo['same_store'];
            $this->insert($data);
        }
        return SUCCESS;
    }

}