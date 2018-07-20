<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


class StoreChangeModel extends BaseModel
{
    protected $table="aq_store_change";
    protected $rule=[];
    protected  $msg=[];

    protected  $storeModel;
    protected  $userModel;
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storeModel=new StoreModel();
        $this->userModel=new UserModel();
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $store=$this->storeModel->getTable();
        $user=$this->userModel->getTable();
        return $this->alias('store_change')
            ->join([$store=>"in_store"],'store_change.in_store=in_store.id','left')
            ->join([$store=>"out_store"],'store_change.out_store=out_store.id','left')
            ->join([$user=>"checkUser"],'store_change.check_user=checkUser.id','left')
            ->join([$user=>"buildUser"],'store_change.build_user=buildUser.id','left');

    }

    public function getFieldArr()
    {
        return array(
            'store_change.id'=>"id",

            'store_change.check_status'=>"check_status",
            'store_change.close_status'=>"close_status",


            'store_change.in_store'=>"in_store",
            'in_store.name'=>"in_store_name",

            'store_change.out_store'=>"out_store",
            'out_store.name'=>"out_store_name",

            'store_change.build_time'=>"build_time",
            'store_change.build_user'=>"build_user",
            'buildUser.name'=>"build_user_name",

            'store_change.check_user'=>"check_user",
            'checkUser.name'=>"check_user_name",

            'store_change.info'=>"info",
            'store_change.item_info'=>"item_info"
        );
    }
}