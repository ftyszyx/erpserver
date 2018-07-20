<?php
/**
 * Created by zyx.
 * Date: 2018-2-1
 * Time: 17:13
 */

namespace app\admin\controller;


use data\model\ConfigModel;
use data\model\LogModel;
use think\Cache;
use think\Db;
use think\Request;

class Config extends BaseController
{
    protected $model;
    protected $logModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->logModel=new LogModel();
        $this->model = new ConfigModel();
    }

    public function all()
    {
        return $this->model->allList();
    }

    //更新缓存
    public  function  updateCache()
    {
        Cache::clear();

//        $this->model->updateCache();
//        $this->model->updateStoreCache();
        return AjaxReturn(SUCCESS);
    }

    //修改系统配置
    public function edit()
    {
        $changeData = request()->post('list/a', '');
        Db::startTrans();
        try{
            //修改配置
            foreach ($changeData as $key=>$value){
                $id=$value['id'];
                $oldinfo=$this->model->where(['id'=>$id])->find();
                $keyname="value";
                if($oldinfo['type']=="int"){
                    $keyname="int_value";
                }
                if($oldinfo[$keyname]!=$value[$keyname])
                {
                    $this->model->updateOne($id,[$keyname=>$value[$keyname]]);
                    $this->logModel->addLog($oldinfo['info'].' 修改为:'.$value[$keyname]);
                }
            }
            $this->configModel->updateConfCache();//更新缓存
            Db::commit();
            return AjaxReturn(SUCCESS);

        }
        catch (\Exception $e){
            \think\Log::record("Exception item:".$e->getMessage()." lines:".var_export($e->getTrace(),true),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }

    }
}