<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 17:31
 * 错误返回值
 */

//0表示正常


define('SUCCESS', 1);

//system
define("SYSTEM_BEGIN", 20);
define("NO_LOGIN", SYSTEM_BEGIN+1);
define("ILLEGAL", SYSTEM_BEGIN+2);
define("SYSTEM_ERROR", SYSTEM_BEGIN+3);
define("NO_PERMISSION", SYSTEM_BEGIN+4);
define("ERROR_FORM", SYSTEM_BEGIN+5);
define("ERROR_DEL_MYSELF", SYSTEM_BEGIN+6);
define("ERROR_ID", SYSTEM_BEGIN+7);
define("UPLOAD_ERROR", SYSTEM_BEGIN+8);
define("CONFIG_ERROR", SYSTEM_BEGIN+9);
define("NO_DATA_UPDATE", SYSTEM_BEGIN+10);
define("ID_ERROR", SYSTEM_BEGIN+11);
define("INSERT_ERROR", SYSTEM_BEGIN+12);
define("USER_NOTEXIT", SYSTEM_BEGIN+13);
define("ID_EMPTY", SYSTEM_BEGIN+14);
define("JSON_ERROR", SYSTEM_BEGIN+15);
define("CHECK_USER_ERROR", SYSTEM_BEGIN+16);
define("CHECK_CLOSE_ERROR", SYSTEM_BEGIN+17);
define("STATUS_ERROR", SYSTEM_BEGIN+18);

//user
define("USER_BEGIN", 100);
define("USER_LOCK", USER_BEGIN+1);
define("USER_ERROR", USER_BEGIN+2);
define("USER_REPEAT", USER_BEGIN+3);

//用户组
define("USER_GROUP_BEGIN",300);
define("USER_GROUP_REPEAT", USER_GROUP_BEGIN+1);

//商店
define("SHOP_BEGIN",400);
define("SHOP_NAME_REPEAT", SHOP_BEGIN+1);
define("SHOP_TYPE_ERROR", SHOP_BEGIN+2);
define("SHOP_DEL_PARENT", SHOP_BEGIN+3);
define("ITEM_NAME_REPEAT", SHOP_BEGIN+4);
define("ITEMTYPE_NAME_REPEAT", SHOP_BEGIN+5);
define("ITEM_CODE_REPEAT", SHOP_BEGIN+6);

//采购订单
define("BUYIN_BEGIN",600);
define("BUYIN_DATA_ERROR", BUYIN_BEGIN+1);
define("BUYIN_STORE_ERROR", BUYIN_BEGIN+2);
define("BUYIN_USER_ERROR", BUYIN_BEGIN+3);
define("BUYIN_ITEM_ERROR", BUYIN_BEGIN+4);
define("BUYIN_ITEM_EMPTY", BUYIN_BEGIN+5);
define("BUYIN_USER_EMPTY", BUYIN_BEGIN+6);
define("BUYIN_ORDER_EMPTY", BUYIN_BEGIN+7);
define("BUYIN_STORE_EMPTY", BUYIN_BEGIN+8);
define("BUYIN_STORE_MAX", BUYIN_BEGIN+9);
define("BUYIN_STORE_DEL_ID_ERROR", BUYIN_BEGIN+10);
define("BUYIN_STORE_DEL_ID_CLOSE", BUYIN_BEGIN+11);
define("BUYIN_STORE_DEL_ID_CHECKED", BUYIN_BEGIN+12);





//仓库
define("STORE_BEGIN",800);
define("STORE_ERROR_DEL",STORE_BEGIN+1);
define("STORE_ERROR_DEL_MAX",STORE_BEGIN+2);
define("STORE_In_EMPTY",STORE_BEGIN+3);
define("STORE_OUT_EMPTY",STORE_BEGIN+4);
define("STORE_CHANGEID_ERROR",STORE_BEGIN+5);
define("STORE_STORE_SAME",STORE_BEGIN+6);
define("STORE_CHANGE_ID_ERROR",STORE_BEGIN+7);
define("STORE_CHECKRULE_ERROR",STORE_BEGIN+8);

//销售

define("SELL_BEGIN",900);
define("SELL",SELL_BEGIN+1);
define("SELL_ASSING_REFUND",SELL_BEGIN+2);
define("SELL_ASSING_NUM_ERROR",SELL_BEGIN+3);
define("SELL_ASSING_STORE_ERROR",SELL_BEGIN+4);
define("SELL_ASSING_NUM_MAX_ERROR",SELL_BEGIN+5);
define("SELL_DEL_EMPTY",SELL_BEGIN+6);
define("SELL_TYPE_ERROR", SELL_BEGIN+7);
define("SELL_TRACKMAN_ERROR", SELL_BEGIN+8);
define("SELL_SHOPORDER_SAME_ERROR", SELL_BEGIN+9);
define("SELL_MERGE_MORETWO_ERROR", SELL_BEGIN+10);
define("SELL_STORE_EMPTY", SELL_BEGIN+11);
define("SELL_IDNUMBER_ERROR", BUYIN_BEGIN+12);

function getErrorInfo($error_code)
{
    $system_error_arr = array(
        //基础变量
        SUCCESS  => '成功',
        NO_LOGIN  => '未登录',
        SYSTEM_ERROR  => '系统错误',
        ILLEGAL  => '非法请求',
        NO_PERMISSION  => '无权限',
        USER_LOCK  => '账号被禁用',
        USER_ERROR=>'账号名或密码错误',
        USER_REPEAT=>'重复用户信息',
        ERROR_FORM=>'输入信息有误',
        ERROR_DEL_MYSELF=>"不能删除自己",
        USER_GROUP_REPEAT=>"用户组重复",
        SHOP_NAME_REPEAT=>"商店名重复",
        SHOP_TYPE_ERROR=>"商品类型不存在",
        SHOP_DEL_PARENT=>"子节点还存在，父节点不能删除",
        ITEM_NAME_REPEAT=>"商品名重复",
        ITEMTYPE_NAME_REPEAT=>"商品类型名重复",
        BUYIN_DATA_ERROR=>"数据有误",
        BUYIN_STORE_ERROR=>"仓库id有误",
        BUYIN_USER_ERROR=>"审核人有误",
        BUYIN_ITEM_ERROR=>"商品信息有误",
        BUYIN_ITEM_EMPTY=>"商品不能为空",
        STORE_ERROR_DEL=>"删除时找不到对应商品",
        STORE_ERROR_DEL_MAX=>"超过了最大可删除数量",
        BUYIN_USER_ERROR=>"用户id错误",
        BUYIN_USER_EMPTY=>"id空",
        BUYIN_ORDER_EMPTY=>"销售订单为空",
        BUYIN_STORE_EMPTY=>"仓库信息为空",
        BUYIN_STORE_MAX=>"入库数量超上限",
        BUYIN_STORE_DEL_ID_ERROR=>"要废除的订单不存在",
        BUYIN_STORE_DEL_ID_CLOSE=>"要废除的订单状态不对",
        STORE_In_EMPTY=>"转入仓库为空",
        STORE_OUT_EMPTY=>"转出仓库为空",
        STORE_CHANGEID_ERROR=>"无此调货单",
        STORE_STORE_SAME=>"转入和转出仓库不能相同",
        STORE_CHANGE_ID_ERROR=>"订单id错误",
        UPLOAD_ERROR=>"上传文件失败",
        BUYIN_STORE_DEL_ID_CHECKED=>"已经审核过",
        CONFIG_ERROR=>"配制问题读取错误",
        NO_DATA_UPDATE=>"没有数据更新",
        SELL_ASSING_REFUND=>"有退货订单，分配失败",
        SELL_ASSING_NUM_ERROR=>"商品数量不合规",
        ID_ERROR=>'id有误',
        USER_NOTEXIT=>"用户不存在",
        INSERT_ERROR=>"插入失败",
        ID_EMPTY=>"ID空",
        SELL_ASSING_STORE_ERROR=>"要审核商品和待配货区商品的仓库不一致",
        SELL_ASSING_NUM_MAX_ERROR=>"待配货区商品超过数量限制",
        JSON_ERROR=>"json格式不对",
        CHECK_CLOSE_ERROR=>"订单已经废弃不能审核",
        CHECK_USER_ERROR=>"非审核用户",
        SELL_DEL_EMPTY=>"废弃原因不能为空",
        SELL_TYPE_ERROR=>"报关标识错误",
        SELL_TRACKMAN_ERROR=>"跟单员不能为空",
        SELL_SHOPORDER_SAME_ERROR=>"平台订单号重复",
        SELL_MERGE_MORETWO_ERROR=>"只能合并两个",
        STORE_CHECKRULE_ERROR=>"格式错误",
        SELL_STORE_EMPTY=>"仓库不能为空",
        SELL_IDNUMBER_ERROR=>"身份证号码错误",
        ITEM_CODE_REPEAT=>"商品编码重复",
        STATUS_ERROR=>"状态不对"
    );
    if(array_key_exists($error_code, $system_error_arr))
    {
        return $system_error_arr[$error_code];
    }else{
        return '未知错误';
    }


}
