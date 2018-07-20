<?php
/**
 * Created by zyx.
 * Date: 2018-2-26
 * Time: 16:36
 */

namespace app\admin\controller;

//导出模板配置

use data\model\ExportModel;
use think\Request;

class Export extends  BaseController
{
    protected $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new ExportModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    private function checkData(&$data)
    {
        $data['name'] = request()->post('name', null);
        $data['value'] = request()->post('value/a', null);
        $data['model'] = request()->post('model', null);
        if(empty($data['value'])==false)
        {
            $data['value']=json_encode($data['value'],JSON_UNESCAPED_UNICODE);
            if($data['value']==false)
            {
                return JSON_ERROR;
            }
        }
        return SUCCESS;
    }

    public  function add()
    {
        $data=array();
        $ret=$this->checkData($data);
        if($ret!=SUCCESS)
        {
            return AjaxReturn($ret);
        }
        if(empty($data['name'])||empty($data['value'])||empty($data['model']))
        {
            return AjaxReturn(ERROR_FORM);
        }
        $ret=$this->model->where(['name'=>$data['name'],'model'=>$data['model']])->find();
        if(!empty($ret))
        {
            return AjaxReturnMsg("模板名重复");
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
        $id= request()->post('id', null);
        if(empty($id))
        {
            return AjaxReturn(ID_EMPTY);
        }
        $templeInfo=$this->model->where(['id'=>$id])->find();
        if(empty($templeInfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $ret=$this->model->delOne($id);
        if(empty($ret)){
            return NO_DATA_UPDATE;
        }
        $this->logModel->addLog("删除模板:".$templeInfo['name']." 模块:".$templeInfo['model']);
        return AjaxReturn($ret);
    }


    public  function  edit()
    {
        $data=array();
        $id= request()->post('id', null);
        if(empty($id))
        {
            return AjaxReturn(ID_EMPTY);
        }
        $templeInfo=$this->model->where(['id'=>$id])->find();
        if(empty($templeInfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $ret=$this->checkData($data);
        if($ret!=SUCCESS)
        {
            return AjaxReturn($ret);
        }
        unset($data['model']);
        unset($data['name']);
        $ret=$this->model->updateOne($id,$data);
        if(empty($ret)){
            return NO_DATA_UPDATE;
        }
        $this->logModel->addLog(json_encode($data));
        return AjaxReturn($ret);
    }
}