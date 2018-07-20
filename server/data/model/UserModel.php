<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;
use app\admin\controller\User;
use think\Db;
use think\Log;
use \think\session;


class UserModel extends BaseModel
{
    protected $table="aq_sys_user";
    protected $rule=[];
    protected  $msg=[];
    protected $userGroupModel;
    protected $moduleModel;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->userGroupModel=new UserGroupModel();
        $this->moduleModel=new ModuleModel();
    }

    public  function  preAll($search,$withfield,$orderInfo)
    {
        return $this->field('password,login_num',true);
    }
    //初始化登录信息
    private function initLoginInfo($user_info)
    {


//        Session::set('is_system', $user_info['is_system']);
//        Session::set('name', $user_info['name']);
//        Session::set('account', $user_info['account']);
//        Session::set('user_group', $user_info['user_group']);
//        //Log::record(sprintf("login userinfo:%s",var_export($user_info,true)),'zyx');
//        if(!empty( $user_info['user_group'])){
//
//            //获取这个用户的所有模块
//            $group_info = $this->userGroupModel->where([ 'id' =>$user_info['user_group']])->find();
//            if(!empty($group_info['module_ids'])){
//                $moduleids=json_decode($group_info['module_ids']);
//                Log::record(sprintf("moduleids:%s",var_export($moduleids,true)),'zyx');
//                //获取所有没权限的
//                $allmodules=$this->moduleModel->where(['need_auth'=>1,"id"=>['notin',$moduleids]])->select();
//                Log::record(sprintf("allmodules:%s",json_encode($allmodules)),'zyx');
//                Session::set('module_ids', $moduleids);
//                Session::set('forbid_module_list',  json_encode($allmodules));
//                Session::set('group_name',  $group_info['name']);
//            }
//        }
//        //用户登录成功钩子
//        hook("userLoginSuccess", $user_info);
//        Log::record("session:".var_export($_SESSION,true));
        return SUCCESS;
    }


    //判断有没有权限
    public function checkAuth($module_id)
    {
        $config=new ConfigModel();
        $userinfo=$config->getUserInfo($this->uid);
        if(empty($userinfo)){
            //Log::record("userinfo empty",'zyx');
            return false;
        }
        $issystem=$userinfo["is_system"];
        if(empty($issystem)==false&&$issystem==true){
            //Log::record('is_system','zyx');
            return true;
        }
        $module_ids=$userinfo["module_ids"];
        if(empty($module_ids)){
            return false;
        }
        if (in_array($module_id,$module_ids)) {
            return true;
        }
        return false;

    }


    /*用户登录
     */
    public function login($user_name, $password = '')
    {
        $this->Logout();
        $condition = array(
            'account' => $user_name,
            'password' => md5($password)
        );
        $user_info=$this->where($condition)->field('account,name,mail,phone,is_system,is_valid,user_group,id')->find();
        //dump($user_info);
        if (! empty($user_info)) {
            if ($user_info['is_valid'] == 0) {
                return USER_LOCK;
            } else {
                //登录成功后增加用户的登录次数
                Session::set('uid', $user_info['id']);
                return SUCCESS;
                //return $this->initLoginInfo($user_info);

            }
        } else
            return USER_ERROR;
    }

    /**
     * 用户退出
     */
    public function Logout()
    {
        Session::set('uid', '');
//        Session::set('is_system', 0);
//        Session::set('module_ids', '');
//        Session::set('name','');
//        Session::set('account','');
//        Session::set('user_group', '');
//        Session::set('forbid_module_list', '');
//        Session::set('group_name', '');
    }



}