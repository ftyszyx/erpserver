<?php
/**
 * Created by zyx.
 * Date: 2018-3-5
 * Time: 17:13
 */

namespace app\admin\controller;

use data\model\LogModel;
use think\Db;
use think\Request;
use data\model\DataBaseModel;

class Database extends  BaseController
{
    protected $model;
    protected $logModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->logModel=new LogModel();
        $this->model = new DataBaseModel();

    }

    public function all()
    {
        return $this->model->allList();
    }

    //保存
    public  function  add()
    {
        $name= request()->post('name', '');
        if(empty($name)){
            return AjaxReturnMsg("请备注名称");
        }
        return $this->model->saveData($name);
    }

    //删除
    public  function  del()
    {
        $id= request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $dataInfo=$this->model->where(['id'=>$id])->find();

        if(empty($dataInfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        //删除文件
        $file_num=$dataInfo['file_num'];
        for($i=1;$i<=$file_num;$i++)
        {
            $path=$this->model->getFullPath($dataInfo['path'],$i);
            if(is_file($path)){
                unlink($path);
            }
        }

        $name=$dataInfo['name'];
        $ret=$this->model->delOne($id);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("删除的备份:".$name);
        }
        return AjaxReturn($ret);

    }

    //删除
    public  function  edit()
    {
        $id= request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $name= request()->post('name', '');
        if(empty($name)){
            return AjaxReturnMsg("请备注名称");
        }
        $dataInfo=$this->model->where(['id'=>$id])->find();

        if(empty($dataInfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $srcName=$dataInfo['name'];
        $ret=$this->model->updateOne($id,['name'=>$name]);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("修改备份:".$srcName." 改成:".$name);
        }
        return AjaxReturn($ret);

    }

    //还原
    public  function  restore()
    {
        $id= request()->post('id', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        return AjaxReturnMsg("功能禁用");
//        return $this->model->restore($id);

    }
}