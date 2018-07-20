<?php
/**
 * Created by zyx.
 * Date: 2018-3-29
 * Time: 17:37
 */

namespace app\admin\controller;


use data\model\InStoreLogModel;
use data\model\LogModel;
use think\Request;
use think\Session;

class InStoreLog extends BaseController
{
    protected $model;
    protected $logModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->logModel=new LogModel();
        $this->model = new InStoreLogModel();

    }


    public function all()
    {
        return $this->model->allList();
    }


    //导出
    public  function  exportCsv()
    {
        $search=request()->post('search/a', null); //标题名
        $headList=request()->post('headlist/a', ''); //标题名
        $filename=request()->post('filename', '');  //文件名
        $nameList=request()->post('namelist/a', ''); //字段名
        $type=request()->post('type', ''); //类型

        Session::set('search',$search);
        Session::set('headlist',$headList);
        Session::set('filename',$filename);
        Session::set('namelist',$nameList);
        Session::set('model','InStoreLog');
        Session::set('type',$type);
        Session::set('modelName','data\model\InStoreLogModel');
        return AjaxReturn(SUCCESS);
    }






}