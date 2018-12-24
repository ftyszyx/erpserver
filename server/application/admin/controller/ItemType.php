<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:19
 */

namespace app\admin\controller;

use data\model\LogModel;
use think\Request;
use data\model\ItemTypeModel;

class ItemType extends BaseController
{
    protected $model;
    protected $logModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->logModel=new LogModel();
        $this->model=new ItemTypeModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    //检查数据
    private function checkData(&$data,$srcdata=null)
    {
        $data['name'] = request()->post('name', null);
        $data['code'] = request()->post('code', null);
        $data['info'] = request()->post('info', null);
        $data['level'] = request()->post('level', null);
        $data['parent_id'] = request()->post('parent_id', null);
        $data['sort_id'] = request()->post('sort_id', null);

        if($this->model->checkSameArr($data,$srcdata,'name')){
            return ITEMTYPE_NAME_REPEAT;
        }
        unsetSame($data,$srcdata,'name','code','info','level','parent_id','sort_id');
        return SUCCESS;
    }

    public  function add()
    {
        \think\Log::record("add",'zyx');
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
        }
        return AjaxReturn($ret);
    }

    public  function  del()
    {
        $id= request()->post('id', '');

        $info=$this->model->where(['parent_id'=>$id])->find();
        if(!empty($info)){
            return AjaxReturn(SHOP_DEL_PARENT);
        }
        $name=$info['name'];
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        //$ret=$this->model->delOne($id);
        $ret=$this->model->updateOne($id,["is_del"=>1]);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("类型id:".$id." 类型:".$name);
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
            return AjaxReturn(ERROR_FORM);
        }
        $name=$oldinfo['name'];
        $checkRes=$this->checkData($data,$oldinfo);
        if($data["parent_id"]==$id ){
            return AjaxReturnMsg("父节点不能是自己");
        }
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }

        $ret=$this->model->updateOne($id,$data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("类型id:".$id."源类型:".$name." 修改内容：".json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }
}