<?php
/*
 * 系统配置
 * */
namespace data\model;
use think\Cache;
use think\Log;

class ConfigModel extends BaseModel{
    protected $table="aq_config";
    protected $rule=[];
    protected  $msg=[];
    protected  $storeModel;
    public function __construct(array $data = [])
    {

        parent::__construct($data);
        $this->storeModel=new StoreModel();
    }

    //获取默认仓库
    public  function getConfig($name){
        $config=Cache::get("config");
        return $config[$name];
    }

    public  function getStoreConfig($storeId){
        $config=Cache::get("store");
        return $config[$storeId];
    }

    public  function getUserInfo($userid,$forceuUpdate=false){

        //$config=Cache::get("user");
        if($forceuUpdate||Cache::tag("user")->has($userid+"")==false){
            $userModel=new UserModel();
            $userGroup=new UserGroupModel();
            $module=new ModuleModel();
            $userinfo=$userModel->where(["id"=>$userid])->find();
            if(empty($userinfo)){
                Log::record("userinfo is empty");
                return array();
            }else{
                $data=array();
                $data["uid"]=$userinfo['id'];
                $data["is_system"]=$userinfo['is_system'];
                $data["name"]=$userinfo['name'];
                $data["account"]=$userinfo['account'];
                $data["user_group"]=$userinfo['user_group'];
                if(!empty( $data['user_group'])){
                    //获取这个用户的所有模块
                    $group_info =$userGroup->where([ 'id' =>$data['user_group']])->find();
                    if(!empty($group_info['module_ids'])){
                        $moduleids=json_decode($group_info['module_ids']);
                        $allmodules=$module->where(['need_auth'=>1,"id"=>['notin',$moduleids]])->select();
                        $data["module_ids"]=$moduleids;
                        $data["forbid_module_list"]=json_encode($allmodules);
                        $data["group_name"]=$group_info['name'];
                    }
                }
                //Log::record("set cache:".var_export($data,true));
                Cache::tag("user")->set($userid+"",$data);
                return $data;
            }
        }
        return Cache::tag("user")->get($userid+"");
    }



    //初始化缓存
    public  function  initCache(){


        if(Cache::has("config")==false){

            $this->updateConfCache();
        }
        if(Cache::has("store")==false){
            $this->updateStoreCache();
        }

    }

    public function updateConfCache()
    {
        Log::record("initconfig_cache",'zyx');
        $configValue=array();
        $ret=$this->select();
        foreach ($ret as $key=>$value){
            if($value['type']=="int"){
                $configValue[$value['name']]=$value['int_value'];
            }
            else{
                $configValue[$value['name']]=$value['value'];
            }
        }
        Cache::set('config',$configValue);
    }

    public function updateStoreCache()
    {
        Log::record("initstore_cache",'zyx');
        $configValue=array();
        $ret=$this->storeModel->select();
        foreach ($ret as $key=>$value){
            $storeInfo=array();
            $storeInfo['sell_num_limit']=$value['sell_num_limit'];
            $storeInfo['name']=$value['name'];
            $storeInfo['check_rule']=json_decode($value['check_rule'],true);
            $configValue[$value['id']]=$storeInfo;
        }
        Cache::set('store',$configValue);
    }


    public  function  getSellBaseValue($sellType){
        if($sellType==SELL_TYPE_BC){
            return $this->getConfig('bc_value');
        }
        else if($sellType==SELL_TYPE_CC){
            return $this->getConfig('cc_value');
        }
        else if($sellType==SELL_TYPE_NS){
            return $this->getConfig('ns_value');
        }
        else if($sellType==SELL_TYPE_NO){
            return $this->getConfig('no_value');
        }
        return 1;
    }


}
