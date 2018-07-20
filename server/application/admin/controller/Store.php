<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 12:09
 */

namespace app\admin\controller;
//仓库

use data\model\ConfigModel;
use data\model\LogModel;
use data\model\StoreModel;
use think\Request;
class Store extends BaseController
{
    protected $model;
    protected $logModel;
    protected  $configModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new StoreModel();
        $this->logModel=new LogModel();
        $this->configModel=new ConfigModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    //检查数据
    private function checkData(&$data,$srcdata)
    {
        $data['admin_name'] = request()->post('admin_name', null);
        $data['phone'] = request()->post('phone', null);
        $data['address'] = request()->post('address', null);
        $data['name'] = request()->post('name', null);
        $data['sell_num_limit'] = request()->post('sell_num_limit', null);
        $data['period_rule'] = request()->post('period_rule', null);
        $data['same_store'] = request()->post('same_store', null);
        if($this->model->checkSameArr($data,$srcdata,'name')){
            return SHOP_NAME_REPEAT;
        }
        unsetSame($data,$srcdata,'admin_name','phone','address','name','sell_num_limit','same_store');
        return SUCCESS;
    }

    public  function add()
    {
        $data=array();

        $checkRes=$this->checkData($data,null);
        if(empty($data['name'])){
            return (ERROR_FORM);
        }
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        $ret=$this->model->addOne($data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
            $this->configModel->updateStoreCache();
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
        $ret=$this->model->updateOne($id,["is_del"=>1]);
        if($ret==SUCCESS)
        {
            $this->logModel->AddLog('仓库名:'.$name);
            //$this->configModel->updateStoreCache();
        }
        return AjaxReturn($ret);
    }

    public  function  updateCheckRule()
    {
        $id = request()->post('id', '');
        $rule = request()->post('check_rule/a', '');
        if(empty($id)){
            return AjaxReturn(ID_EMPTY);
        }
        $oldInfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldInfo)){
            return AjaxReturn(ID_ERROR);
        }
        if(empty($rule))
        {
            return AjaxReturn(ERROR_FORM);
        }

        $ruleStr=json_encode($rule,JSON_UNESCAPED_UNICODE);
        if($ruleStr==false)
        {
            return AjaxReturn(STORE_CHECKRULE_ERROR);
        }

        $ret=$this->model->updateOne($id,['check_rule'=>$ruleStr]);
        if($ret==SUCCESS)
        {
            $this->logModel->AddLog('仓库名:'.$oldInfo['name']." 修改审核规则:".$ruleStr);
            $this->configModel->updateStoreCache();
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
        if(empty($oldinfo)){
            return AjaxReturn(ID_ERROR);
        }
        $name=$oldinfo['name'];
        $checkRes=$this->checkData($data,$oldinfo);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        if(!empty($data['same_store']))
        {
            if($data['same_store']==$id){
                return AjaxReturnMsg("关联仓库不能是自己");
            }
        }
        $ret=$this->model->updateOne($id,$data);
        if($ret==SUCCESS)
        {
            $this->logModel->AddLog('仓库名:'.$name." 修改内容:".json_encode($data,JSON_UNESCAPED_UNICODE));
            $this->configModel->updateStoreCache();
        }
        return AjaxReturn($ret);
    }


}