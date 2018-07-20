<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018-1-12
 * Time: 15:54
 */

namespace data\model;


class LogModel extends BaseModel
{
    protected $table="aq_log";
    protected $rule=[];
    protected  $msg=[];
    protected  $userModel;
    protected  $moduleModel;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->userModel=new UserModel();
        $this->moduleModel=new ModuleModel();
    }


    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $module=$this->moduleModel->getTable();
        $user=$this->userModel->getTable();
        return $this->alias('log')
            ->join([$module=>"module"],["log.controller=module.controller","log.method=module.method"],'left')
            ->join([$user=>"user"],'log.userid=user.id','left');
    }

    public function getFieldArr()
    {
        return array(
            'log.info'=>"info",
            'log.time'=>"time",
            'log.link'=>"link",
            'user.name'=>"user_name",
            'module.name'=>"module_name"
        );
    }

    //增加一条日志
    public  function  AddLog($info='',$link='',$typeInfo='info')
    {
        $data=array();
        $data['controller']=\think\Request::instance()->controller();
        $data['method']=\think\Request::instance()->action();
        $data['time']=time();
        $data['info']='['.$typeInfo.']'.$info;
        $data['userid']=$this->uid;
        $data['link']=$link;
        $this->insert($data);
    }
}