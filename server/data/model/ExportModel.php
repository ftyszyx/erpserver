<?php
/**
 * Created by zyx.
 * Date: 2018-2-26
 * Time: 16:37
 */

namespace data\model;


class ExportModel extends BaseModel
{
    protected $table="aq_export";
    protected $rule=[];
    protected  $msg=[];
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}