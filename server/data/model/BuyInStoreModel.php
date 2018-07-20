<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:02
 */

namespace data\model;


class BuyInStoreModel extends BaseModel
{
    protected $table="aq_buy_instore";
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
        return $this->alias('buyinstore')
            ->join([$store=>"store"],'buyinstore.store_id=store.id','left')
            ->join([$user=>"check_user"],'buyinstore.check_user=check_user.id','left')
            ->join([$user=>"build_user"],'buyinstore.build_user=build_user.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'buyinstore.id'=>"id",

            'buyinstore.check_status'=>"check_status",
            'buyinstore.close_status'=>"close_status",


            'buyinstore.store_id'=>"store_id",
            'buyinstore.buy_order'=>"buy_order",
            'store.name'=>"store_name",

            'buyinstore.build_time'=>"build_time",
            'buyinstore.build_user'=>"build_user",
            'build_user.name'=>"build_user_name",

            'buyinstore.check_user'=>"check_user",
            'check_user.name'=>"check_user_name",

            'buyinstore.info'=>"info",
            'buyinstore.item_info'=>"item_info"
        );
    }

}