<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


class ShopModel extends BaseModel
{
    protected $table="aq_shop";
    protected $rule=[];
    protected  $msg=[];
    protected  $shopType;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->shopType=new ShopTypeModel();
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $shoptype=$this->shopType->getTable();
        return $this->alias('shop')
            ->join([$shoptype=>"shoptype"],'shop.shop_type=shoptype.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'shop.name'=>"name",
            'shop.id'=>"id",
            'shop.shop_type'=>"shop_type",
            'shoptype.name'=>"shop_type_name",
            'shop.valid_expire_time'=>"valid_expire_time");
    }
}