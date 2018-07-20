<?php
/**
 * Created by zyx.
 * Date: 2018-1-26
 * Time: 11:58
 */

//审核状态（销售订单状态）
define("CHECK_NO", 0); //待审核
define("CHECK_OK", 1);//已审核待配货
define("ASSIGN_OK", 2);//已配货


//作废状态
define("DELETE_NO", 0);
define("DELETE_OK", 3);


//支付状态
define("PAY_NO", 0);
define("PAY_OK", 1);

//退款状态
define("REFUND_MIN", 0);
define("REFUND_NO", 0);
define("REFUND_OK", 1);
define("REFUND_REQ", 2);
define("REFUND_MAX", 2);

//报关标识
define("SELL_TYPE_MIN", 0);
define("SELL_TYPE_CC", 0);// 3的倍数
define("SELL_TYPE_NS", 1);
define("SELL_TYPE_BC", 2); //6的倍数
define("SELL_TYPE_NO", 3); //不拆单
define("SELL_TYPE_MAX", 3);

//订单vip标识
define("SELL_VIP_MIN", 0);
define("SELL_VIP_NOMRAL", 0);
define("SELL_VIP_PHOTO", 1);
define("SELL_VIP_VIDEO", 2);
define("SELL_VIP_DATE", 3);
define("SELL_VIP_MAX", 3);


//一些单前缀
define("BUYIN_PRE",'PO'); //采购单
define("BUYINSTORE_PRE",'PAO'); //入库单
define("BUYOUT_PRE",'PRO'); //出库单
define("STORECHANGE_PRE",'WAO'); //调货单
define("SELL_PRE",'SO'); //售货单
define("SELLOUT_PRE",'SPO');//出货单
define("SELLBACK_PRE",'SPB');//退单

//入库状态
define("INSTORE_NO",0);
define("INSTORE_OK",1);
define("INSTORE_PART",2);

//销售订单默认用户id
define("DEFAULT_SELL_BUILD_USER",4);


//拆单和合单基数
define("SPLITE_SELL_BASE",3);

//操作类型
define("INSTORE_TYPE",1); //入库单
define("INSTORE_DEL_TYPE",2); //入库单废弃
define("INSTORE_UPDATE_TYPE",3); //入库单修改
define("OUTSTORE_TYPE",4); //出库单
define("OUTSTORE_DEL_TYPE",5); //出库单废弃
define("OUTSTORE_UPDATE_TYPE",6); //出库单修改
define("CHANGESTORE_TYPE",7); //调货单
define("CHANGESTORE_DEL_TYPE",8); //调货单废弃
define("CHANGESTORE_UPDATE_TYPE",9); //调货单修改
define("SELLOUT_TYPE",10);//销售配货
define("SELLOUT_DEL_TYPE",11);//销售配货废弃
define("SELL_DEL_TYPE",12);//销售单废弃

