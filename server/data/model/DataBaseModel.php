<?php
/**
 * Created by zyx.
 * Date: 2018-3-5
 * Time: 17:14
 */

namespace data\model;


use think\Db;
use ZipArchive;

class DataBaseModel extends BaseModel
{
    protected $table="aq_database";
    protected $rule=[];
    protected  $msg=[];
    protected $line_end="\r\n";
    protected $sqlname="backsql.sql";
    protected $zipname="backsql.zip";
    protected $save_path='';
    protected $logModel;
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->logModel=new LogModel();
        $this->save_path=$save_path='public'.DS.'sqlbackup'.DS;
    }

    public  function  getFullPath($path)
    {
        return ROOT_PATH.$path;
    }


    public function getDatabaseList()
    {

        $databaseList = Db::query("SHOW TABLE STATUS");
        return $databaseList;
    }

    //写入表格
    public  function db_table_schemas($table) {

        $dump = "DROP TABLE IF EXISTS `{$table}`;".$this->line_end;
        $sql = "SHOW CREATE TABLE {$table}";
        $row = DB::query($sql);
        if(empty($row)){
            return false;
        }
        //\think\Log::record("table:".var_export($row,true));
        $dump .= $row[0]['Create Table'];
        $dump .= ";{$this->line_end}";
        return $dump;
    }

    //插入数据
    public function db_table_insert_sql($table,$start,$size) {
        $data = '';

        $sql = "SELECT * FROM `{$table}` LIMIT {$start},{$size}";
        $result = Db::query($sql);
        if (!empty($result)) {
            foreach($result as $row) {
                $tmp = '';
                //\think\Log::record("row:".var_export($row,true));
                $tmp .= '(';
                foreach($row as $k => $v) {
                    if($v===null){
                        $tmp .= "null,";
                    }else{
                        $value = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $v);
                        $tmp .= "'" . $value . "',";
                    }
                }
                $tmp = rtrim($tmp, ',');
                $tmp .= ")";
                $data .= "INSERT INTO `{$table}` VALUES {$tmp};".$this->line_end;
            }
            //$tmp = rtrim($tmp, ",{$this->line_end}");
            return $data;
        } else {
            return false ;
        }
    }


    //保存
    public  function  saveData($name,$addLog=true)
    {
        $tables=$this->getDatabaseList();
        if (empty($tables))
        {
            return AjaxReturnMsg("数据库是空");
        }
        //\think\Log::record("tables:".var_export($tables,true));

        $path=ROOT_PATH.$this->save_path;
        $path = rtrim($path, DS) . DS;
        if(is_dir($path)==false){
            if(mkdir($path)==false)
            {
                return AjaxReturnMsg("文件夹创建失败");
            }
        }

        $insert_max_num=200; //单次插入最大数量

        $fullPath=$this->getFullPath($this->save_path.$this->sqlname);
        $myfile=fopen($fullPath,"w");
        if(empty($myfile)){
            fclose($myfile);
            return AjaxReturnMsg("写文件失败");
        }
        foreach ($tables as $table){
            $dump = '';
            $tableName=$table["Name"];
            if($tableName==$this->logModel->getTable()||$tableName==$this->getTable())
            {
                //这两个表不保存
                continue;
            }

            $dump .= "-- ----------------------------{$this->line_end}";
            $dump .= "-- Table structure for {$tableName}{$this->line_end}";
            $dump .= "-- ----------------------------{$this->line_end}";
            $row = $this->db_table_schemas($tableName);
            if(empty($row)){
                fclose($myfile);
                return AjaxReturnMsg($tableName."写入失败");
            }
            $dump .= $row;
            $dump .= $this->line_end;
            $dump .= "-- ----------------------------{$this->line_end}";
            $dump .= "-- records of {$tableName}{$this->line_end}";
            $dump .= "-- ----------------------------{$this->line_end}";
            $sql = "SELECT COUNT(*) FROM `{$tableName}`";
            $result = Db::query($sql);
            if(empty($result)){
                return AjaxReturnMsg($tableName."读取表格行数失败");
            }
            //\think\Log::record("rownum:".var_export($result,true),'zyx');
            $totalNum=$result[0]["COUNT(*)"];
            $page=ceil($totalNum/$insert_max_num);
            fwrite($myfile,$dump);

            for ($i=0;$i<$page;$i++){
                //分页写入
                $start=$insert_max_num*$i;
                $row= $this->db_table_insert_sql($tableName,$start,$insert_max_num);
                if(empty($row)){
                    fclose($myfile);
                    return AjaxReturnMsg($tableName."写入数据失败");
                }
                fwrite($myfile,$row);
                unset($row);
                fflush($myfile);
            }
        }
        fclose($myfile);
        $zip = new ZipArchive;//新建一个ZipArchive的对象
        $md5file = md5_file($fullPath);
        $zippath=$this->getFullPath($this->save_path.$md5file).".zip";
        $res=$zip->open($zippath, ZipArchive::OVERWRITE|ZipArchive::CREATE);
        \think\Log::record("fullpath:".$fullPath,'zyx');
        \think\Log::record("zippath:".$zippath,'zyx');
        \think\Log::record("md5:".$md5file,'zyx');
        if ($res=== TRUE)
        {
            $zip->addFile($fullPath,$this->sqlname);//假设加入的文件名是image.txt，在当前路径下
            $zip->close();
            unlink($fullPath);
        }else{
            return AjaxReturnMsg("压缩失败:".$res);
        }


        $data=array();
        $data['name']=$name;
        $data['time']=time();
        $data['path']=$this->save_path.$md5file.".zip";
        $ret=$this->insert($data);

        if(empty($ret))
        {
            return AjaxReturnMsg("数据库出错");
        }
        if($addLog)
        {
            $this->logModel->addLog("增加备份:".json_encode($data,JSON_UNESCAPED_UNICODE));
        }

        return AjaxReturn(SUCCESS);
    }

    //还原
    public  function  restore($id)
    {

        $dataInfo=$this->where(['id'=>$id])->find();
        if(empty($dataInfo))
        {
            return AjaxReturn(ID_ERROR);
        }
        $name=$dataInfo['name'];
        $path=$this->getFullPath( $dataInfo['path']);
        \think\Log::record(" name:{$name} path:{$path}",'zyx');
        $zip = new ZipArchive;//新建一个ZipArchive的对象

        $extactpath=$this->getFullPath($this->save_path);
        if ($zip->open($path) === TRUE)
        {
            $zip->extractTo($extactpath);//假设解压缩到在当前路径下images文件夹的子文件夹php
            $zip->close();//关闭处理的zip文件
        }else{
            return AjaxReturnMsg("文件不存在".$path);
        }
        $filepath=$this->getFullPath($this->save_path.$this->sqlname);
        $myfile = fopen($filepath, "r");
        if(empty($myfile)){
            return AjaxReturnMsg("文件不存在".$filepath);
        }
        $sqlstr="";
        DB::startTrans();
        try{
            while(!feof($myfile)) {

                $linestr=fgets($myfile);
                if(startsWith(trim($linestr),"--")==true){
                    continue;
                }
                $sqlstr.=$linestr;
                if(endsWith(trim($linestr),';')==true){
                    \think\Log::record("restore sql:".$sqlstr,'zyx');
                    Db::query($sqlstr);
                    $sqlstr="";
                }
            }
            Db::commit();
            fclose($myfile);
            unlink($filepath);
            $this->logModel->addLog("还原备份成功:".$name);
            return AjaxReturn(SUCCESS);
        }
        catch (\Exception $e){
            fclose($myfile);
            unlink($filepath);
            \think\Log::record("restore database:".$e->getMessage(),'zyx');
            DB::rollback();
            return AjaxReturnMsg($e->getMessage());
        }

    }



}