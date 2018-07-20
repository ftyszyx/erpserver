<?php
/**
 * Created by zyx.
 * Date: 2018-1-16
 * Time: 10:52
 */

namespace app\admin\controller;

use data\model\LogModel;
use data\model\UserGroupModel;
use think\Db;
use think\Request;
use think\Session;

class UserGroup extends BaseController
{

    protected $model;
    protected $logModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new UserGroupModel();
        $this->logModel=new LogModel();
    }

    //获取列表
    public  function all()
    {
        return $this->usergroup->allList();
    }

    //增加
    public  function add()
    {
        $data=array();
        $data['name'] = request()->post('name', null);
        $data['module_ids']= request()->post('module_ids', null);
        $ret=$this->usergroup->addOne($data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }

    //更新
    public  function  edit(){
        $data=array();
        $id = request()->post('id', '');
        $data['name'] = request()->post('name', null);
        $data['module_ids']= request()->post('module_ids', null);
        $oldinfo=$this->model->where(['id'=>$id])->find();
        if(empty($oldinfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        unsetSame($data,$oldinfo,'name','module_ids');
        $ret=$this->usergroup->updateOne($id,$data);

        if($ret==SUCCESS)
        {
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }

    //删除
    public  function  del(){
        $delId = request()->post('id', null);
        $mygorup=Session::get('user_group');
        if($mygorup==$delId){
            return AjaxReturn(ERROR_DEL_MYSELF);
        }
        $oldinfo=$this->model->where(['id'=>$delId])->find();
        if(empty($oldinfo))
        {
            return AjaxReturn(ERROR_ID);
        }
        $name=$oldinfo['name'];
        $ret=$this->usergroup->delOne($delId);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog('name:'.$name);
        }
        return AjaxReturn($ret);
    }

}