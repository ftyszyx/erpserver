<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


class ModuleModel extends BaseModel
{
    protected $table="aq_module";
    protected $rule=[];
    protected  $msg=[];

    public function getModuleIdByModule($controller, $action)
    {
        $condition = array(
            'controller' => $controller,
            'method' => $action,
            'module' => request()->module()
        );
        $res = $this->where($condition)->find();
        return $res;
    }
}