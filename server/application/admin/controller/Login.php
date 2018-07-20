<?php
/**
 * Created by zyx.
 * Date: 2018-1-15
 * Time: 16:08
 */

namespace app\admin\controller;
\think\Loader::addNamespace('data', 'data/');

use data\model\ConfigModel;
use data\model\UserModel;
use think\Controller;
use think\Log;
use think\Request;
use think\Session;

class Login extends Controller
{

    public $user;

    public  function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->init();
    }

    private  function init()
    {
        $this->user=new UserModel();
    }

    /**
     * 用户登录
     *
     * @return number
     */
    public function login()
    {
        Log::record("method".request()->method());
        if(request()->isOption()){
            return AjaxReturn(SUCCESS);
        }
        $user_name = request()->post('username','');
        $password = request()->post('password','');
        $ret = $this->user->login($user_name, $password);
        return AjaxReturn($ret);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $this->user->Logout();
        return AjaxReturn(SUCCESS);
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo()
    {
        //Log::record("getUserInfo:".var_export($_SESSION,true));
        $uid=Session::get('uid');
        if(empty($uid)){
            return AjaxReturn(NO_LOGIN);
        }
        $config=new ConfigModel();
        $userinfo=$config->getUserInfo($uid);
        //Log::record("userinfo:".var_export($userinfo,true));
        return AjaxReturn(SUCCESS,array(
            'account'=>$userinfo["account"],
            'user_group'=>$userinfo["user_group"],
            'forbid_module_list'=> json_decode($userinfo["forbid_module_list"],true)
        ));
    }
}