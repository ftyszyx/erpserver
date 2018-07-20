<?php
/**
 * Created by zyx.
 * Date: 2018-1-23
 * Time: 12:09
 */

namespace app\admin\controller;
use data\model\LogModel;
use think\Request;
//日志
class Log extends BaseController
{
	  protected $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model=new LogModel();
    }

    public  function all()
    {
        return $this->model->allList();
    }


}