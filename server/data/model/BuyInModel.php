<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 * 入库单
 */
namespace data\model;


use think\Log;

class BuyInModel extends BaseModel
{
    protected $table="aq_buy_in";
    protected $rule=[];
    protected  $msg=[];

    protected $storeModel;
    protected $userModel;
    protected  $inStoreModel;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storeModel=new StoreModel();
        $this->userModel=new UserModel();
        $this->inStoreModel=new BuyInStoreModel();

    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $store=$this->storeModel->getTable();
        $user=$this->userModel->getTable();
        return $this->alias('buyin')
            ->join([$store=>"store"],'buyin.store_id=store.id','left')
            ->join([$user=>"check_user"],'buyin.check_user=check_user.id','left')
            ->join([$user=>"build_user"],'buyin.build_user=build_user.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'buyin.id'=>"id",

            'buyin.check_status'=>"check_status",
            'buyin.in_store_status'=>"in_store_status",
            'buyin.close_status'=>"close_status",
            'buyin.supplier'=>"supplier",


            'buyin.store_id'=>"store_id",
            'store.name'=>"store_name",

            'buyin.build_time'=>"build_time",
            'buyin.build_user'=>"build_user",
            'build_user.name'=>"build_user_name",

            'buyin.check_user'=>"check_user",
            'check_user.name'=>"check_user_name",

            'buyin.info'=>"info",
            'buyin.item_info'=>"item_info"
        );
    }


    public  function  GetInStoreInfo(&$inStoreItem,$buyInOrder){
        $inStoreInfo=$this->inStoreModel->where(['buy_order'=>$buyInOrder,'close_status'=>DELETE_NO])->select();
        if(empty($inStoreInfo)){
            return SUCCESS;
        }
        //所有对应的入库商品
        foreach ($inStoreInfo as $key=>$value){
            $itemInfo=json_decode($value['item_info'],true);
            GetItemInfo($inStoreItem,$itemInfo,null);
        }
        return SUCCESS;
    }

    //更新商品的入库状态
    public  function  updateInStoreState($buyInOrder)
    {

        $buyInInfo=$this->where(['id'=>$buyInOrder])->find();
        if(empty($buyInOrder)){
            return SYSTEM_ERROR;
        }

        $inStoreItem=array();
        if($this->GetInStoreInfo($inStoreItem,$buyInOrder)!=SUCCESS)
        {
            return SYSTEM_ERROR;
        }
        Log::record(sprintf("updateInStoreState instore:%s  ",var_export($inStoreItem,true)),'zyx');

        $in_storenum=0;//已经入库的总数量
        foreach ($inStoreItem as $key=>$value){
            $in_storenum=$in_storenum+$value;
        }
        $buyItemArr=json_decode($buyInInfo['item_info'],true);
        Log::record(sprintf("updateInStoreState buyin:%s  ",var_export($buyItemArr,true)),'zyx');

        if($in_storenum==0){
            $inStoreSatuts=INSTORE_NO;
            //没有入库
            foreach ($buyItemArr as $key=>$value){
                $buyItemArr[$key]['inStore']=0;
            }
        }
        else{
            $inStoreSatuts=INSTORE_OK;
            foreach ($buyItemArr as $key=>$value){
                $itemid=$value['id'];
                $itemNum=0;
                if(isset($inStoreItem[$itemid])==true){
                    $itemNum=$inStoreItem[$itemid];
                }
                $buyItemArr[$key]['inStore']=$itemNum;
                if($value['num']>$itemNum){
                    $inStoreSatuts=INSTORE_PART;
                }
                else if($value['num']<$itemNum){
                    return BUYIN_STORE_MAX;
                }
            }
        }
        Log::record(sprintf("updateInStoreState setstatus:%s iteminfo:%s ",$inStoreSatuts,var_export($buyItemArr,true)),'zyx');
        $this->updateOne($buyInOrder,['in_store_status'=>$inStoreSatuts,'item_info'=>json_encode($buyItemArr,JSON_UNESCAPED_UNICODE)]);
        return SUCCESS;
    }

}