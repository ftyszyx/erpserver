<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 12:00
 */

namespace app\admin\controller;

use data\model\LogModel;
use data\model\ShopTypeModel;
use think\Request;

class ShopType extends BaseController
{
    protected $model;
    protected $logModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new ShopTypeModel();
        $this->logModel=new LogModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    //检查数据
    private function checkData(&$data,$srcdata)
    {
        $data['name'] = request()->post('name', null);
        if($this->model->checkSameArr($data,$srcdata,'name')){
            return SHOP_NAME_REPEAT;
        }
        return SUCCESS;
    }

    public  function add()
    {
        $data=array();
        $checkRes=$this->checkData($data,null);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        if(empty($data['name'])){
            return (ERROR_FORM);
        }
        $ret=$this->model->addOne($data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }

    public  function  del()
    {
        $id= request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $oldinfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldinfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $name=$oldinfo['name'];
        $ret=$this->model->delOne($id);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("商店类型:".$name);
        }
        return AjaxReturn($ret);
    }

    public  function  edit()
    {

        $data=array();
        $id = request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $oldinfo=$this->model->where(['id'=>$id])->find();
        $name=$oldinfo['name'];
        if(empty($oldinfo)){
            return AjaxReturn(ERROR_FORM);
        }
        $checkRes=$this->checkData($data,$oldinfo);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        $ret =$this->model->updateOne($id,$data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("商店类型:".$name." 修改内容:".json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }
}