<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:17
 */

namespace app\admin\controller;

use data\model\StoreItemModel;
use think\Request;
use think\Session;

//仓库商品列表
class StoreItem extends BaseController
{
    protected $model;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new StoreItemModel();

    }

    public  function  exportNameProcess($name,$data)
    {
        return $data;
    }

    public  function all()
    {
        return $this->model->allList();
    }

    public  function  exportCsv()
    {
        $search=request()->post('search/a', null); //标题名
        $headlist=request()->post('headlist/a', ''); //标题名
        $filename=request()->post('filename', '');  //文件名
        $namelist=request()->post('namelist/a', ''); //字段名

        Session::set('search',$search);
        Session::set('headlist',$headlist);
        Session::set('filename',$filename);
        Session::set('namelist',$namelist);
        Session::set('modelName','data\model\StoreItemModel');
        Session::set('model','StoreItem');
        return AjaxReturn(SUCCESS);
    }
}