<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function AjaxReturn($err_code, $data = [])
{
    // return $retval;
    $rs = [
        'code' => $err_code."",
        'message' => getErrorInfo($err_code)
    ];
    if (! empty($data))
        $rs['data'] = $data;
    return json_encode($rs);
}

function AjaxReturnMsg($msg)
{
    // return $retval;
    $rs = [
        'code' => SYSTEM_ERROR."",
        'message' => $msg
    ];
    return json_encode($rs);
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    return $length === 0 ||
        (substr($haystack, -$length) === $needle);
}

//钩子
function hook($hook, $params = [])
{
    // 钩子调用
    \think\Hook::listen($hook, $params);
}

function getOrderId($pre,$uid,$order=0){
    if($order==0)
    {
        $order=rand(1,9999);
    }
    return $pre.date('ymdHis').sprintf("%03d%04d",$uid,$order);
}

function GetItemInfo(&$arr,$addInfo,$delInfo)
{
    \think\Log::record(sprintf("dest:%s addInfo:%s delInfo:%s",var_export($arr,true),
        var_export($addInfo,true),var_export($delInfo,true)),'zyx');
    if(!empty($addInfo)){
        foreach ($addInfo as $key=>$item)
        {
            $itemId=$item['id'];
            $num=$item['num'];
            //
            if(isset($arr[$itemId])==false){
                $arr[$itemId]=$num;
            }
            else{
                $arr[$itemId]=$arr[$itemId]+$num;
            }
        }
    }
    if(!empty($delInfo))
    {
        foreach ($delInfo as $key=>$item)
        {
            $itemId=$item['id'];
            $num=$item['num'];
            if(isset($arr[$itemId])==false){
                \think\Log::record("item id is less 0",'zyx');
                return SYSTEM_ERROR;
            }
            else{
                if($arr[$itemId]>$num){
                    $arr[$itemId]=$arr[$itemId]+$num;
                    return SUCCESS;
                }
                else{
                    \think\Log::record("item id is less ".$num,'zyx');
                    return SYSTEM_ERROR;
                }

            }
        }
    }
    return SUCCESS;
}


function unsetSame(&$data,$oldinfo)
{
    if($oldinfo==null)
    {
        return;
    }
    if (func_num_args() > 2)
    {
        $paramnum=func_num_args();
        for ($index=2; $index < $paramnum; $index++) {
            $keyname = func_get_arg($index);
            //\think\Log::record("keyname:{$keyname}"." data:".var_export($data,true),'zyx');
            //\think\Log::record("keyname:{$keyname}",'zyx');
            if(array_key_exists($keyname,$data)==false){
                \think\Log::record("keyname:{$keyname} is unset",'zyx');
                continue;
            }
            if((empty($data[$keyname])==false)&&(empty($oldinfo[$keyname])==false))
            {
                if($data[$keyname]==$oldinfo[$keyname])
                {
                    \think\Log::record("unset:{$keyname}",'zyx');
                    unset($data[$keyname]);
                    continue;
                }
            }
            else if($data[$keyname]===null)
            {
                //\think\Log::record("keyname:{$keyname} is null",'zyx');
                unset($data[$keyname]);
            }
            else if($data[$keyname]===$oldinfo[$keyname]){
                //\think\Log::record("keyname:{$keyname} is ==",'zyx');
                unset($data[$keyname]);
            }
            if(empty($data[$keyname])==false){
                $typename=gettype($data[$keyname]);
                \think\Log::record("change type:".$typename,'zyx');
                if(gettype($data[$keyname])=="string"){
                    $data[$keyname]= trim($data[$keyname]);
                    \think\Log::record("trim ok:".$data[$keyname],'zyx');
                }
            }
            
        }
    }
}

function getUtf8($src)
{
    $encode = mb_detect_encoding($src, array('GB2312',"GBK",'UTF-8'));
    //\think\Log::record("filetype".$encode."src:".$src,'zyx');
    if($encode!='UTF-8')
    {
        $src= mb_convert_encoding($src,'utf-8',$encode);
      //  \think\Log::record("conver to ".$src,'zyx');
    }

    return $src;
}


function checkIdCard($idcard){

    // 只能是18位
    if(strlen($idcard)<18){
        return false;
    }
    return true;
    // 取出本体码
    $idcard_base = substr($idcard, 0, 17);

    // 取出校验码
    $verify_code = substr($idcard, 17, 1);

    // 加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

    // 校验码对应值
    $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

    // 根据前17位计算校验码
    $total = 0;
    for($i=0; $i<17; $i++){
        $total += substr($idcard_base, $i, 1)*$factor[$i];
    }

    // 取模
    $mod = $total % 11;

    // 比较校验码
    if($verify_code == $verify_code_list[$mod]){
        return true;
    }else{
        return false;
    }

}


