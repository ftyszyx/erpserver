<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 14:19
 */

namespace app\admin\controller;

use data\model\LogModel;
use think\Request;
use data\model\ItemModel;

class Item extends BaseController
{
    protected $model;
    protected $logModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new ItemModel();
        $this->logModel=new LogModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }

    //检查数据
    private function checkData(&$data,$srcdata=null)
    {
        $data['name'] = request()->post('name', null);
        $data['weight'] = request()->post('weight', null);
        $data['code'] = request()->post('code', null);
        $data['barcode'] = request()->post('barcode', null);
        $data['short_name'] = request()->post('short_name', null);
        $data['type'] = request()->post('type', null);
        $data['sell_base_num'] = request()->post('sell_base_num', null);
        $data['check_limit'] = request()->post('check_limit', null);
        $data['milk_period'] = request()->post('milk_period', null);
        $data['sort_id'] = request()->post('sort_id', null);
        if(!empty($data['name'])){
            if($this->model->checkSameArr($data,$srcdata,'name')){
                return ITEM_NAME_REPEAT;
            }
        }

        if(!empty($data['code'])){
            if($this->model->checkSameArr($data,$srcdata,'code')){
                return ITEM_CODE_REPEAT;
            }
        }

        unsetSame($data,$srcdata,'name','weight','sort_id','code','barcode','short_name',"milk_period",'type','sell_base_num','check_limit');
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
        $data['build_time']=time();
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
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }
        $iteminfo=$this->model->where(['id'=>$id])->find();
        $itemname=$iteminfo['name'];
        if(empty($iteminfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $ret=$this->model->updateOne($id,["is_del"=>1]);
        //$ret=$this->model->delOne($id);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("删除的商品id:".$id." 商品名：".$itemname);
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
        $checkRes=$this->checkData($data,$oldinfo);
        if($checkRes!=SUCCESS){
            return AjaxReturn($checkRes);
        }
        if(!empty($data['sell_base_num'])){
            $ret=$this->checkAuth($this->control,"updateSellValue");
            if($ret!=SUCCESS){
                return AjaxReturn($ret);
            }
        }
        if(!empty($data['check_limit'])){
            $ret=$this->checkAuth($this->control,"updateCheckLimit");
            if($ret!=SUCCESS){
                return AjaxReturn($ret);
            }
        }
        $ret=$this->model->updateOne($id,$data);
        if($ret==SUCCESS)
        {
            $this->logModel->addLog("修改商品id:".$id." 修改内容:".json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        return AjaxReturn($ret);
    }
}
