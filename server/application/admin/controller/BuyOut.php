<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:18
 */

namespace app\admin\controller;

use data\model\BuyInModel;
use data\model\StoreItemModel;
use data\model\UserModel;
use think\Request;
use data\model\BuyOutModel;
use data\model\ItemModel;
use data\model\StoreModel;
class BuyOut extends BuyInStore
{
    protected $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new BuyOutModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    public  function  add(){
        return $this->addCommon('out');
    }

    public  function  del(){
        return $this->delCommon('out');
    }

    //审核
    public  function  checkOk()
    {
        return $this->checkCommon();
    }

    public  function  edit()
    {
        return $this->updateCommon('out');
    }
}