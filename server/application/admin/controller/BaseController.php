<?php

namespace app\admin\controller;
\think\Loader::addNamespace('data', 'data/');

use data\model\ConfigModel;
use data\model\LogModel;
use data\model\UserModel;
use think\Cache;
use think\Controller;
use think\Db;
use think\Request;
use think\Log;
use data\model\UserGroupModel as UserGroupModel;
use data\model\UserModel as User;
use data\model\ModuleModel as Module;
use think\Session;

class BaseController extends Controller
{
    protected $module=null;
    protected  $moduleid=null;

    protected $user=null;
    protected $usergroup=null;
    protected $uid=null;
    protected  $model=null;//模块名
    protected  $control=null;//控制器名
    protected  $action=null;//方法名
    protected  $logModel;
    protected  $userModel;
    protected $configModel;


    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->user=new User();
        $this->module=new Module();
        $this->usergroup=new UserGroupModel();
        $this->logModel=new LogModel();
        $this->userModel=new UserModel();
        $this->configModel=new ConfigModel();
        $this->configModel->initCache();
        $this->init();
    }

    protected  function init()
    {
        Log::record("init",'zyx');
        $this->uid=$this->user->uid;

        if(request()->isOption()){
            echo AjaxReturn(SUCCESS);
        }
        $this->control=\think\Request::instance()->controller();
        $this->action=\think\Request::instance()->action();
        \think\Log::record("control:".$this->control." action:".$this->action,'zyx');
        if(($this->control=="Sell")&&($this->action=="addoneorder")){
            //直接进函数验证
            //\think\Log::record("go to action",'zyx');
            return;
        }
        \think\Log::record("go to action test".($this->control=="Sell"),'zyx');
        \think\Log::record("go to action test".($this->action=="syncLogicstics"),'zyx');

        if(($this->control=="Sell")&&($this->action=="synclogicstics")){
            //直接进函数验证
            \think\Log::record("go to action",'zyx');
            return;
        }
        if(empty($this->uid)){
            echo AjaxReturn(NO_LOGIN);
            exit();
        }
        //$userinfo=$this->userModel->where(['id'=>$this->uid])->find();
        $config=new ConfigModel();
        $userinfo=$config->getUserInfo($this->user->uid);
        if(empty($userinfo))
        {
            $this->userModel->Logout();
            echo AjaxReturn(USER_NOTEXIT);
            exit();
        }
        if(request()->isGet())
        {
            if($this->control=="ItemTotal"){
                //汇总
            }else{
                //导表
                $this->export_csv();
            }
        }else if(request()->isPost()){
                $ret =$this->checkAuth($this->control,$this->action);
                if($ret!=SUCCESS){
                    echo AjaxReturn($ret);
                    exit();
                }
        }else{
            Log::record("isnot post",'zyx');
            echo AjaxReturn(ILLEGAL);
            exit();
        }
    }

    //检查权限
    protected function  checkAuth($control,$action){
        $config=new ConfigModel();

        $userinfo=$config->getUserInfo($this->user->uid);
        if(empty($userinfo)){
            return NO_PERMISSION;
        }
        $is_system=$userinfo["is_system"];
        Log::record(sprintf("contrl:%s,action:%s issytem:%d",$this->control,$this->action,$is_system),'zyx');
        $moduleinfo=$this->module->getModuleIdByModule($control,$action);
        if(!empty($is_system)&&$is_system){
            $can_access =true;
        } elseif(empty($moduleinfo)){
            //还没设置时，就不用验证
            $this->moduleid = 0;
            $can_access =true;
        } elseif ($moduleinfo["need_auth"] == 0) {
            //不用验证
            $this->moduleid = $moduleinfo['id'];
            $can_access = true;
        } else {
            $this->moduleid = $moduleinfo['id'];
            $can_access = $this->user->checkAuth($this->moduleid);
        }
        Log::record("can_access:".$can_access,'zyx');
        if($can_access){
            //验证通过
            Log::record("check ok",'zyx');
            return SUCCESS;
        }
        else{
            return NO_PERMISSION;
        }
    }

    //审核
    protected  function  checkCommon($checkStatus=CHECK_OK)
    {
        $id= request()->post('id/a', '');
        if(empty($id)){
            return AjaxReturn(ERROR_FORM);
        }

        Db::startTrans();
        try{
            foreach ($id as $key => $val) {
                    $info=$this->model->where(['id'=>$val])->find();
                    if(empty($info))
                    {
                        return AjaxReturn(ERROR_ID);
                    }
                    if($info['close_status']==DELETE_OK)
                    {
                        return AjaxReturn(CHECK_CLOSE_ERROR);
                    }
                    if($info['check_user']!=$this->uid)
                    {
                        return AjaxReturn(CHECK_USER_ERROR);
                    }
                    if($checkStatus==CHECK_OK){
                        if(SUCCESS!=$this->model->updateOne($val,["check_status"=>$checkStatus,"check_time"=>time()]))
                        {
                            return AjaxReturn(SYSTEM_ERROR);
                        }
                    }
                    else{
                        if(SUCCESS!=$this->model->updateOne($val,["check_status"=>$checkStatus,"check_time"=>0]))
                        {
                            return AjaxReturn(SYSTEM_ERROR);
                        }
                    }
                }
            DB::commit();
            $this->logModel->addLog("订单:".json_encode($id));
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            \think\Log::record("checkCommon:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturn(SYSTEM_ERROR);
        }

    }



    //http://blog.csdn.net/tim_phper/article/details/77581071  输出压缩文件

    function export_csv()
    {
        Log::record("export csv",'zyx');
        $search=Session::get('search'); //标题名
        $headlist=Session::get('headlist'); //标题名
        $filename=Session::get('filename');  //文件名
        $namelist=Session::get('namelist'); //字段名
        $modelName=Session::get('modelName');
        $logModelName=Session::get('model');
        $subtype=Session::get('type');
        Log::record(sprintf("search:%s headlist:%s  filename:%s namelist:%s  modelname:%s",
            var_export($search,true),var_export($headlist,true),$filename ,var_export($namelist,true),$modelName),'zyx');
        if(empty($modelName))
        {
            echo AjaxReturn(ILLEGAL);
            exit();
        }
        $model=new $modelName;
        if(preg_match( '/MSIE/i', $_SERVER['HTTP_USER_AGENT'] )){
            $filename = urlencode($filename);
            $filename = iconv('UTF-8', 'GBK//IGNORE', $filename);
        }


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
        //header ( "Content-Disposition:filename=" . iconv ( "UTF-8", "GB18030", "query_user_info" ) . ".csv" );
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        if($logModelName=="InStoreLog"&&$subtype=="total")
        {
            $headlist=array("商品编号",'商品名称','商品排序号',"仓库",'期初数量','入库数量','退回数量','销售数量','期末数量');
        }
        else if($logModelName=="Sell"&&$subtype=="item_total")
        {
            $headlist=array("平台名称",'商品名称',"仓库",'商品排序号','商品类型排序号','数量');
        }
        //输出Excel列名信息
        foreach ($headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] = iconv('UTF-8', 'GBK//IGNORE', $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headlist);

        $tempFileName = md5(microtime(true)).".csv";
        $tempPath=ROOT_PATH.'public'.DS.'temp'.DS.date('Ymd') . DS;
        //Log::record("dir:".$tempPath,'zyx');
        if(is_dir($tempPath)==false){
            if(mkdir($tempPath,0777,true)==false)
            {
                return AjaxReturnMsg("文件夹创建失败");
            }
        }
        $filepath=$tempPath.$tempFileName;
        $logfile=fopen($filepath, 'a');
        fputcsv($logfile, $headlist);
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;


        if($logModelName=="InStoreLog"&&$subtype=="total"){
            $totalCount=$model->preAll($search,false,null)->where($search)->order(['time'=>'asc'])->count();
            $pagenum=intval($totalCount/$limit);
            $totalArray=array();//汇总信息
            //汇总
            for ($i=0;$i<$pagenum+1;$i++){
                //每一个行
                $start=$i*$limit;
                $export_data = $model->preAll($search,true,null)->where($search)->limit($start.','.$limit)->select();
                foreach ( $export_data as $item )
                {
                    $id=$item['item_id'].'-'.$item['store_id'];
                    $logType=$item['type'];
                    if(isset($totalArray[$id])==false) {
                        $totalArray[$id]=[
                            'item_id'=>$item['item_id'],
                            'item_name'=>$item['item_name'],
                            'item_code'=>$item['item_code'],
                            'item_sort_id'=>$item['item_sort_id'],
                            'store_name'=>$item['store_name'],
                            'store_id'=>$item['store_id'],
                            'before_num'=>$item['before_num'],
                            'after_num'=>0,
                            'in_store'=>0,
                            'out_store'=>0,
                            'sell'=>0,

                        ];
                    }

                    if($logType==INSTORE_TYPE||$logType==INSTORE_UPDATE_TYPE||$logType==INSTORE_DEL_TYPE
                        ||$logType==CHANGESTORE_UPDATE_TYPE||$logType==CHANGESTORE_TYPE||$logType==CHANGESTORE_DEL_TYPE){
                        $totalArray[$id]['in_store']+=$item['change_num'];
                        $totalArray[$id]['after_num']+=$item['change_num'];
                    }
                    else if($logType==OUTSTORE_UPDATE_TYPE||$logType==OUTSTORE_TYPE||$logType==OUTSTORE_DEL_TYPE){
                        $totalArray[$id]['out_store']-=$item['change_num'];
                        $totalArray[$id]['after_num']+=$item['change_num'];
                    }
                    else if($logType==SELLOUT_DEL_TYPE||$logType==SELLOUT_TYPE){
                        $totalArray[$id]['sell']-=$item['change_num'];
                        $totalArray[$id]['after_num']+=$item['change_num'];
                    }
                    //Log::record("info".var_export($totalArray,true),'zyx');
                }
                // 将已经写到csv中的数据存储变量销毁，释放内存占用
                unset($export_data);
                ob_flush();
                flush();
            }
            foreach ($totalArray as $totalitem){
                $rows = array();
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['item_code']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['item_name']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['item_sort_id']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['store_name']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['before_num']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['in_store']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['out_store']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['sell']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE',$totalitem['after_num']);
                fputcsv($fp, $rows);
                fputcsv($logfile, $rows);
            }
        }
        else  if($logModelName=="Sell"&&$subtype=="item_total")
        {
            $totalCount=$model->preAll($search,false,null)->where($search)->count();
            $pagenum=intval($totalCount/$limit);
            $totalArray=array();//汇总信息
            //汇总
            for ($i=0;$i<$pagenum+1;$i++){
                //每一个行
                $start=$i*$limit;
                $export_data = $model->preAll($search,true,null)->where($search)->limit($start.','.$limit)->select();
                foreach ( $export_data as $item )
                {
                    $id=$item['item_id'].'-'.$item['store_id'];
                    if(isset($totalArray[$id])==false) {
                        $totalArray[$id]=[
                            'item_id'=>$item['item_id'],
                            'item_name'=>$item['item_name'],
                            'shop_name'=>$item['shop_name'],
                            'shop_id'=>$item['shop_id'],
                            'item_sort_id'=>$item['item_sort_id'],
                            'item_type_sort_id'=>$item['item_type_sort_id'],
                            'store_name'=>$item['store_name'],
                            'store_id'=>$item['store_id'],
                            'total_num'=>0,
                        ];
                    }
                    $totalArray[$id]['total_num']+=$item['num'];

                    //Log::record("info".var_export($totalArray,true),'zyx');
                }
                // 将已经写到csv中的数据存储变量销毁，释放内存占用
                unset($export_data);
                ob_flush();
                flush();
            }
            foreach ($totalArray as $totalitem){
                $rows = array();
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['shop_name']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['item_name']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['store_name']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['item_sort_id']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['item_type_sort_id']);
                $rows[] = iconv('UTF-8', 'GBK//IGNORE', $totalitem['total_num']);
                fputcsv($fp, $rows);
                fputcsv($logfile, $rows);
            }
        }
        else{
            $totalCount=$model->preAll($search,false,null)->where($search)->count();
            $fileNum=intval($totalCount/$limit);
            for ($i=0;$i<$fileNum+1;$i++){
                //每一个文件
                $start=$i*$limit;
                $export_data = $model->preAll($search,true,null)->where($search)->limit($start.','.$limit)->select();
                foreach ( $export_data as $item ) {
                    //每一行
                    $rows = array();
                    foreach ($namelist as $keyname) {
                        $itemText=$model->exportNameProcess($keyname,$item[$keyname]);
                        //Log::record("get:{$itemText} key:{$keyname} ",'zyx');
                        $rows[] = iconv('UTF-8', 'GBK//IGNORE', $itemText);
                        //Log::record("get:{$itemText} key:{$keyname} ok",'zyx');
                    }
                    fputcsv($fp, $rows);
                    fputcsv($logfile, $rows);
                }

                // 将已经写到csv中的数据存储变量销毁，释放内存占用
                unset($export_data);
                ob_flush();
                flush();
            }
        }


        Session::set('search',null);
        Session::set('headlist',null);
        Session::set('filename',null);
        Session::set('namelist',null);
        Session::set('modelName',null);
        Session::set('type',null);

        $url_path=str_replace(ROOT_PATH,'',$filepath);

        $data=array();
        $data['controller']=$logModelName;
        $data['method']="exportCsv";
        $data['time']=time();
        if($logModelName=="InStoreLog"&&$subtype=="total")
        {
            $data['info']='[info]'."导出入库汇总";
        }else if($logModelName=="Sell"&&$subtype=="item_total")
        {
            $data['info']='[info]'."导出商品汇总";
        }
        else{
            $data['info']='[info]'."导出表格";
        }
        $data['userid']=$this->uid;
        $data['link']=$url_path;
        $this->logModel->insert($data);

        exit();
    }

}
