<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


class SellAssignModel extends BaseModel
{
    protected $table="aq_sell_assign";
    protected $rule=[];
    protected  $msg=[];

    protected  $userModel;
    protected  $storeModel;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->userModel=new UserModel();
        $this->storeModel=new StoreModel();
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $user=$this->userModel->getTable();
        $store=$this->storeModel->getTable();
        return $this->alias('sell_assign')
            ->join([$user=>"buildUser"],'sell_assign.build_user=buildUser.id','left')
            ->join([$store=>"store"],'sell_assign.store_id=store.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'sell_assign.id'=>"id",
            'sell_assign.info'=>"info",
            'sell_assign.build_time'=>"build_time",
            'sell_assign.build_user'=>"build_user",
            'sell_assign.order_info'=>"order_info",
            'sell_assign.close_status'=>"close_status",
            'sell_assign.del_info'=>"del_info",
            'store.name'=>"store_name",

            'sell_assign.store_id'=>"store_id",
            'sell_assign.total_num'=>"total_num",
            'buildUser.name'=>"build_user_name"
        );
    }

}