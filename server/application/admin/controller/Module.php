<?php
/**
 * Created by zyx.
 * Date: 2018-1-18
 * Time: 17:51
 */

namespace app\admin\controller;
use think\Request;

class Module extends BaseController
{
    //获取所有模块列表
    public  function all()
    {
        return $this->module->allList();
    }
}