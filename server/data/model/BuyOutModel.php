<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018-1-12
 * Time: 15:44
 */

namespace data\model;


class BuyOutModel extends BaseModel
{
    protected $table="aq_buy_out";
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
        return $this->alias('buyout')
            ->join([$store=>"store"],'buyout.store_id=store.id','left')
            ->join([$user=>"check_user"],'buyout.check_user=check_user.id','left')
            ->join([$user=>"build_user"],'buyout.build_user=build_user.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'buyout.id'=>"id",

            'buyout.check_status'=>"check_status",
            'buyout.close_status'=>"close_status",


            'buyout.store_id'=>"store_id",
            'buyout.buy_order'=>"buy_order",
            'store.name'=>"store_name",

            'buyout.build_time'=>"build_time",
            'buyout.build_user'=>"build_user",
            'build_user.name'=>"build_user_name",

            'buyout.check_user'=>"check_user",
            'check_user.name'=>"check_user_name",

            'buyout.info'=>"info",
            'buyout.item_info'=>"item_info"
        );
    }

}