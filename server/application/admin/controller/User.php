<?php
/**
 * Created by zyx.
 * Date: 2018-1-15
 * Time: 11:26
 */

namespace app\admin\controller;


use data\model\LogModel;
use data\model\UserGroupModel;
use data\model\UserModel;
use think\Db;
use think\Request;

class User extends BaseController
{
    /**
     * 添加 后台用户
     */
    protected  $logModel;
    protected  $model;
    protected  $userGroupModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->logModel=new LogModel();
        $this->model=new UserModel();
        $this->userGroupModel=new UserGroupModel();
    }

    public function add()
    {
        $data=array();
        $data['account'] = request()->post('account', '');
        if(empty($data['account'])){
            return AjaxReturn(ERROR_FORM);
        }
        $data['user_group']  = request()->post('user_group', '');
        if(!empty($data['user_group'])){
            if(!$this->userGroupModel->checkValid($data['user_group'])){
                return AjaxReturn(ERROR_FORM);
            }
        }
        $data['password'] = md5('123456');
        $data['mail']= request()->post('mail', '');
        $data['name'] = request()->post('name', '');
        $data['phone']= request()->post('phone', '');

        if($this->user->checkSameArr($data,null,"account","mail","name","phone"))
        {
            return AjaxReturn(USER_REPEAT);
        }
        $data['reg_time']=time();
        $ret = $this->user->addOne($data);
        if($ret==SUCCESS)
        {
            $temparr=array();
            $temparr['account']=$data['account'];
            $temparr['user_group']=$data['user_group'];
            $temparr['mail']=$data['mail'];
            $temparr['name']=$data['name'];
            $temparr['phone']=$data['phone'];
            $this->logModel->addLog(json_encode($temparr,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }

    //删除用户
    public  function del()
    {
        $delId = request()->post('id', '');
        if($delId==$this->uid){
            return AjaxReturn(ILLEGAL);
        }
        $oldinfo=$this->model->where(['id'=>$delId])->find();
        if(empty($oldinfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $name=$oldinfo['name'];
        $ret=$this->user->delOne($delId);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("用户:".$name);
        }
        return AjaxReturn($ret);
    }

    //修改用户信息
    public  function edit()
    {
        $data=array();
        $uid = request()->post('id', '');
        if(empty($uid)){
            return AjaxReturnMsg("uid空");
        }
        $data['account'] = request()->post('account', null);
        $data['mail']= request()->post('mail', null);
        $data['name'] = request()->post('name', null);
        $data['phone']= request()->post('phone', null);
        $group = request()->post('user_group', null);
        if(empty($data['user_group'])==false){
            $ret=$this->checkAuth($this->control,"updateGroup");
            if($ret!=SUCCESS){
                return AjaxReturnMsg("无权限修改用户组");
            }
            if(!$this->usergroup->validate($group)){
                return AjaxReturnMsg("用户组不存在");
            }
        }
        $data['user_group']= $group;

        $user_info=$this->user->where(['id'=>$uid])->find();
        if(empty($user_info))
        {
            return AjaxReturn(ID_ERROR);
        }
        \think\Log::record("before:".var_export($data,true),'zyx');
        unsetSame($data,$user_info,"account","mail","name","phone",'user_group');
        \think\Log::record("after:".var_export($data,true),'zyx');
        $ret=$this->user->updateOne($uid,$data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog(json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }


    //获取用户列表
    public  function all()
    {
        return $this->user->allList();
    }

    //获取具有某个权限的所有用户
    public  function  allAuthUser()
    {
        $moduleID = request()->post('moduleID', null);
        $allModules=$this->usergroup->where('module_ids','EXP ','REGEXP '.'"'.$moduleID.'\\,*"')->select();
        $groupIds=array();
        foreach ($allModules as $key=>$value){
            $groupIds[]=$value['id'];
        }
        $getData=array();
        $res=$this->user->where('user_group','in',$groupIds)->select();
        foreach ($res as $key=>$value){
            $getData[]=$value->toArray();
        }
        return AjaxReturn(SUCCESS,["list"=>$getData]);
    }


    //设置是否可用
    public  function  changeValid(){
        $is_valid = request()->post('is_valid', 0);
        $uid = request()->post('id', '');
        $ret=$this->user->updateOne($uid,["is_valid"=>$is_valid]);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog('设置是否有效:'.$is_valid);
        }
        return AjaxReturn($ret);
    }

    //修改密码
    public function changePassword(){
        $newpass = request()->post('newpass', '');
        if (empty($newpass)) {
            return AjaxReturn(ERROR_FORM);
        }
        $ret=$this->user->updateOne($this->uid,["password"=>md5($newpass)]);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog();
        }
        return AjaxReturn($ret);
    }


    //修改用户组
    public  function updateGroup()
    {


    }


}