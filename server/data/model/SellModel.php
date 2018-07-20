<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


use think\Log;

class SellModel extends BaseModel
{
    protected $table="aq_sell";
    protected $rule=[];
    protected  $msg=[];

    protected  $storeModel;
    protected  $userModel;
    protected  $shopModel;
    protected $itemModel;
    protected $itemTypeModel;
    protected $storeItemModel;


    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storeModel=new StoreModel();
        $this->userModel=new UserModel();
        $this->shopModel=new ShopModel();
        $this->itemModel=new ItemModel();
        $this->storeItemModel=new StoreItemModel();
        $this->itemTypeModel=new ItemTypeModel();

    }

    //字段值特殊处理
    public  function  exportNameProcess($name,$data)
    {
        //Log::record("process:{$name} {$data}",'zyx');
        if($name=="status"){
            if($data===CHECK_NO){
                return "待审核";
            }
            else if($data===CHECK_OK){
                return "待配货";
            }
            else if($data===ASSIGN_OK){
                return "已配货";
            }
            else if($data===DELETE_OK){
                return "已废弃";
            }
        }
        else if($name=="sell_vip_type")
        {
            if($data===SELL_VIP_NOMRAL){
                return "普通";
            }
            else if($data===SELL_VIP_VIDEO){
                return "视频";
            }
            else if($data===SELL_VIP_PHOTO){
                return "图片";
            }
            else if($data===SELL_VIP_DATE){
                return "日期";
            }
        }
        else if($name=="pay_status"){
            if($data===PAY_NO){
                return "未支付";
            }
            else if($data===PAY_OK){
                return "已支付";
            }
        }
        else if($name=="refund_status"){
            if($data===REFUND_NO){
                return "正常";
            }
            else if($data===REFUND_OK){
                return "已退款";
            }
            else if($data===REFUND_REQ){
                return "退款中";
            }
        }
        else if($name=="pay_time"||$name=="check_time"||$name=="order_time"||$name=="build_time"){
            //Log::record("process time",'zyx');
            return date("y-m-d H:i:s",$data);
        }
        else if($name=="shop_order"||$name=="user_id_number"||$name=="user_phone"){
            //Log::record("process num",'zyx');
            return "\t".$data;
        }
        return $data;
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $store=$this->storeModel->getTable();
        $user=$this->userModel->getTable();
        $shop=$this->shopModel->getTable();
        $item=$this->itemModel->getTable();
        $storeItem=$this->storeItemModel->getTable();
        $itemType=$this->itemTypeModel->getTable();

        $this->alias('sell');
        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"store")==true){
            $this->join([$store=>"store"],'sell.store_id=store.id','left');
        }
        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"check_user")==true){
            $this->join([$user=>"check_user"],'sell.check_user=check_user.id','left');
        }
        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"build_user")==true){
            $this->join([$user=>"build_user"],'sell.build_user=build_user.id','left');
        }
        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"shop")==true){
            $this->join([$shop=>"shop"],'sell.shop_id=shop.id','left');
        }
        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"item")==true){
            $this->join([$item=>"item"],'sell.item_id=item.id','left');
        }

        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"item_type")==true){
            $this->join([$itemType=>"item_type"],'item.type=item_type.id','left');
        }

        if(($onlyjoin==false)||$this->needJoin($search,$orderInfo,"store_item")==true){
            $this ->join([$storeItem=>"store_item"],'sell.store_id=store_item.store_id and sell.item_id=store_item.itemid','left');;
        }
        return $this;
    }


    public function getFieldArr()
    {
      return array(
            'sell.id'=>"id",
            'sell.status'=>"status",
            'sell.refund_status'=>"refund_status",
            'sell.pay_status'=>"pay_status",
            'sell.del_info'=>'del_info',
            'sell.shop_id'=>"shop_id",
            'sell.shop_order'=>"shop_order",
            'sell.customer_name'=>"customer_name",
            'sell.customer_addr'=>"customer_addr",
            'sell.info'=>"info",
            'sell.user_info'=>"user_info",
            'sell.pay_time'=>"pay_time",
            'sell.discount'=>"discount",
            'sell.user_phone'=>"user_phone",
            'sell.pay_money'=>"pay_money",
            'sell.user_id_number'=>'user_id_number',
            'sell.customer_account'=>'customer_account',
            'sell.store_id'=>"store_id",
            'sell.build_time'=>"build_time",
            'sell.build_user'=>"build_user",
            'sell.check_user'=>"check_user",
            'sell.check_time'=>"check_time",

            'sell.info'=>"info",
            "sell.assign_order"=>"assign_order",

            'sell.item_id'=>"item_id",
            'sell.num'=>"num",


            'sell.sell_type'=>"sell_type",
            'sell.logistics'=>"logistics",
            'sell.track_man'=>"track_man",
            'sell.sell_vip_type'=>"sell_vip_type",
            'sell.sell_vip_info'=>"sell_vip_info",
            'sell.logistics_merge'=>"logistics_merge",

            'sell.order_time'=>"order_time",

            'sell.customer_province'=>"customer_province",
            'sell.customer_city'=>"customer_city",
            'sell.customer_area'=>"customer_area",
            'sell.send_user_name'=>"send_user_name",
            'sell.send_user_phone'=>"send_user_phone",
            'sell.unit_price'=>"unit_price",
            'sell.freight_price'=>"freight_price",
            'sell.service_price'=>"service_price",
            'sell.freight_unit_price'=>"freight_unit_price",
            'sell.service_unit_price'=>"service_unit_price",

            'store.name'=>"store_name",
            'item.milk_period'=>"item_milk_period",
            'item.name'=>"item_name",
            'item.short_name'=>"item_short_name",
            'item.sort_id'=>"item_sort_id",
            'item_type.sort_id'=>'item_type_sort_id',
            'check_user.name'=>"check_user_name",
            'build_user.name'=>"build_user_name",
            'shop.name'=>"shop_name",
            'store_item.in_store'=>"in_store_num"

        );
    }


    //获取待配货的storeid
    public  function  getCheckedStoreId()
    {
        $sellinfo=$this->where(['check_status'=>CHECK_OK,'close_status'=>DELETE_NO])->find();
        if(empty($sellinfo)){
            return 0;
        }
        return $sellinfo['store_id'];
    }

    //获取待配货的商品数量
    public  function  getCheckedNum()
    {
        $sellList=$this->where(['check_status'=>CHECK_OK,'close_status'=>DELETE_NO])->select();
        $totalNum=0;
        foreach ($sellList as $key =>$value)
        {
            $totalNum+=$value['num'];
        }
        return $totalNum;
    }
}