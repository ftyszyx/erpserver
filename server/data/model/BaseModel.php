<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 * model基类
 */
namespace data\model;
use think\Debug;
use think\Log;
use think\Model;
use think\Db;
use think\Session;
use think\Validate;

class BaseModel extends Model{
    protected $rule = [];
    protected $msg = [];
    protected $Validate;

    public $uid;//用户user id
    //public $module_ids;//允许的操作模块
    //public  $is_system;//是否是管理员



    public function __construct($data = []){
        parent::__construct($data);
        $this->initModel();
        $this->Validate = new Validate($this->rule, $this->msg);
        $this->Validate->extend('no_html_parse', function ($value, $rule) {
            return true;
        });

    }

    //检查是否有效
    public  function  checkValid($id){
        $count=$this->where(['id'=>$id])->count();
        return $count>0;
    }

    //查询预处理(要返回的字段)
    public function preAll($search,$withfield,$orderInfo)
    {
        if($withfield==true){
            return $this->InitJoinArr($search,$orderInfo,false)->field($this->getFieldArr());
        }else{
            return $this->InitJoinArr($search,$orderInfo,true);
        }
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        return $this;
    }

    public function needJoin($search,$orderInfo,$tablename)
    {
        if(empty($search)==false){
            foreach ($search as $key=>$value){
                $itemArr=explode('.',$key);
                if(count($itemArr)==2&&$itemArr[0]==$tablename){
                    return true;
                }
            }
        }

        if(empty($orderInfo)==false){
            foreach ($orderInfo as $key=>$value){
                $itemArr=explode('.',$key);
                if(count($itemArr)==2&&$itemArr[0]==$tablename){
                    return true;
                }
            }
        }

        return false;
    }

    public function getFieldArr()
    {
        return array();
    }

    //获取所有，同时支持分页
    public function allList()
    {
        $andCon =request()->post('and', true);
        $search_req =request()->post('search/a', null);
        $curPage =request()->post('page', 0);
        $listRow =request()->post('rownum', 0);
        $orderInfo=request()->post('order/a', null);

        if(empty($search_req)){
            $totalNum=$this->count();
        }else{
            if($andCon){
                $totalNum=$this->preAll($search_req,false,$orderInfo)->where($search_req)->count();
            }
            else{
                $totalNum=$this->preAll($search_req,false,$orderInfo)->whereOr($search_req)->count();
            }
        }

        $needPage=false;
        if($curPage>0&&$listRow>0)
        {
            $needPage=true;
        }

        $start=($curPage-1)*$listRow;
        Log::record(sprintf ("curpage:%d,listrow:%d,search:%s order:%s totalnum:%d start:%d needpage:%d",$curPage,$listRow,var_export($search_req,true),
            var_export($orderInfo,true),$totalNum,$start,$needPage),'zyx');


        if($needPage){
            if($totalNum>1000){
                Log::record("get big total:",'zyx');
                $this->InitJoinArr($search_req,$orderInfo,true);
                $tablealias=$this->getOptions("alias");
                $tablealiasname=$this->getTable();
                //用id去取
                if(empty($tablealias)==false){
                    $tablealiasname=$tablealias[$tablealiasname];
                }
                $idname=$tablealiasname.".id";
                if($andCon){
                    $subquery= $this->field($idname)->where($search_req)->limit($start,$listRow)->order($orderInfo)->buildSql();
                } else{

                    $subquery= $this->field($idname)->whereOr($search_req)->limit($start,$listRow)->order($orderInfo)->buildSql();
                }
                $this->preAll($search_req,true,$orderInfo)->order($orderInfo);
                $this->join($subquery.' a','a.id='.$idname);
            }else{
                Log::record("get min total:",'zyx');
                $this->order($orderInfo);
                $this->preAll($search_req,true,$orderInfo);
                if($andCon){
                    $this->where($search_req);
                } else{
                    $this->whereOr($search_req);
                }
                $this->limit($start,$listRow);
            }

        }  else {

            $this->order($orderInfo);
            $this->preAll($search_req,true,$orderInfo);
            if($andCon){
                $this->where($search_req);
            } else{
                $this->whereOr($search_req);
            }
        }
        //Log::record("get selecttext:".$this->select(false),'zyx');
        $res=$this->select();
        $getData=Array();
        if(empty($res)){
            return AjaxReturn(SUCCESS,["list"=>$getData,"num"=>$totalNum]);
        }
        foreach ($res as $key=>$value){
            $getData[]=$value->toArray();
        }
        return AjaxReturn(SUCCESS,["list"=>$getData,"num"=>$totalNum]);
    }



    public function initModel(){
        $this->uid=Session::get('uid');
        //$this->is_system=Session::get('is_system');
        //$this->module_ids=Session::get('module_ids');
    }

    //检查要修改或要增加的数据是否有重复
    public function checkSame($key_name,$value){
        $count = $this->where([
            $key_name => $value
        ])->count();
        if ($count > 0) {
            return true;
        }
        return false;
    }

    //批量检查是否有重复的数据  $srcData:原来的数据  $changeData：要修改的数据
    public function checkSameArr($changeData,$srcData){
        $checkArr=array();
        $paramNum=func_num_args();
        for ($index=2; $index < $paramNum; $index++) {
            $keyname = func_get_arg($index);
            if(isset($changeData[$keyname])==true){
                if(!empty($srcData)){
                    if($srcData[$keyname]!=$changeData[$keyname]){
                        $checkArr[$keyname]=$changeData[$keyname];
                    }
                }
                else{
                    $checkArr[$keyname]=$changeData[$keyname];
                }
            }
        }
        Log::record("checkarr:".var_export($checkArr,true),'zyx');
        if(empty($checkArr)){
            return false;
        }
        $count = $this->whereOr($checkArr)->count();
        if ($count > 0) {
            return true;
        }
        return false;
    }

    //增加一项
    public  function addOne($data)
    {
        //Log::record("add:".var_export($data,true),'zyx');
        $ret=$this->insert($data);
        //Log::record("ret:".var_export($ret,true),'zyx');
        if(empty($ret)){
            return SYSTEM_ERROR;
        }
        return SUCCESS;
    }

    //更新一项
    public  function updateOne($id,$data)
    {
        $ret=$this->where(['id'=>$id])->update($data);

        if(empty($ret)){
            return NO_DATA_UPDATE;
        }
        return SUCCESS;
    }

    public  function  delOne($id)
    {
        $ret=$this->where(['id'=>$id])->delete();
        if(empty($ret)){
            return SYSTEM_ERROR;
        }
        return SUCCESS;
    }
}
?>