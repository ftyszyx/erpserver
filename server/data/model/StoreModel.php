<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


class StoreModel extends BaseModel
{
    protected $table="aq_store";
    protected $rule=[];
    protected  $msg=[];

    public  function  __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $store_name=$this->getTable();
        return $this->alias('store')
            ->join([$store_name=>"same_store"],'same_store.id=store.same_store','left');
    }

    public function getFieldArr()
    {
        return array(
            'store.id'=>"id",
            'store.name'=>"name",
            'store.admin_name'=>"admin_name",
            'store.phone'=>"phone",
            'store.is_del'=>"is_del",
            'store.address'=>"address",
            'store.sell_num_limit'=>"sell_num_limit",
            'store.check_rule'=>"check_rule",
            'store.period_rule'=>"period_rule",
            'store.same_store'=>"same_store",
            'same_store.name'=>"same_store_name"
        );
    }

}