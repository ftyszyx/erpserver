<?php
/**
 * Created by zyx.
 * Date: 2018-1-12
 * Time: 16:01
 */
namespace data\model;


use think\Log;

class StoreItemModel extends BaseModel
{
    protected $table="aq_store_item";
    protected $rule=[];
    protected  $msg=[];

    protected $itemModel;
    protected $storeModel;
    protected $itemTypeModel;

    public function __construct(array $data = [])
    {
        $this->itemModel=new ItemModel();
        $this->storeModel=new StoreModel();
        $this->itemTypeModel=new ItemTypeModel();

        parent::__construct($data);
    }

    public function InitJoinArr($search,$orderInfo,$onlyjoin)
    {
        $item=$this->itemModel->getTable();
        $store=$this->storeModel->getTable();
        $storetype=$this->itemTypeModel->getTable();
        return $this->alias('store_item')
            ->join([$item=>"item"],'store_item.itemid=item.id','left')
            ->join([$store=>"store"],'store_item.store_id=store.id','left')
            ->join([$storetype=>"itemtype"],'item.type=itemtype.id','left');
    }

    public function getFieldArr()
    {
        return array('item.name'=>"item_name",
            'item.code'=>"item_code",
            'item.barcode'=>"item_barcode",
            'item.type'=>"item_type_id",
            'itemtype.name'=>"item_type",
            'store.name'=>"store_name",
            'store_item.store_id'=>"store_id",
            'store_item.on_way'=>"on_way",
            'store_item.id'=>'id',
            'store_item.itemid'=>'itemid',
            'store_item.in_store'=>'in_store',
            'store_item.in_sale'=>'in_sale');
    }

    public  function  getItemNum($itemId,$storeid,$key){
        $oldInfo=$this->where(['itemid'=>$itemId,'store_id'=>$storeid])->find();
        if(empty($oldInfo)){
            return 0;
        }
        return $oldInfo[$key];
    }

    //向仓库中加商品  in_store  in_sale
    public  function addItem($itemid,$num,$storeid,$key)
    {
        $storeInfo=$this->storeModel->where(['id'=>$storeid])->find();
        if(!empty($storeInfo['same_store'])){
            $ret=$this->updateItem($itemid,$num,$storeInfo['same_store'],$key,true);
            if($ret!=SUCCESS){
                return $ret;
            }
        }
        return $this->updateItem($itemid,$num,$storeid,$key,true);
    }

    //向仓库中减商品
    public  function delItem($itemid,$num,$storeid,$key)
    {
        $storeInfo=$this->storeModel->where(['id'=>$storeid])->find();
        if(!empty($storeInfo['same_store'])){
            $ret=$this->updateItem($itemid,$num,$storeInfo['same_store'],$key,false);
            if($ret!=SUCCESS){
                return $ret;
            }
        }
        return $this->updateItem($itemid,$num,$storeid,$key,false);
    }

    protected  function updateItem($itemid,$num,$storeid,$key,$addorDel)
    {
        Log::record(sprintf("update item itemid:%s,storeid:%s addordel:%d key:%s",$itemid,$storeid,$addorDel,$key),'zyx');

        $oldInfo=$this->where(['itemid'=>$itemid,'store_id'=>$storeid])->find();
        if(empty($oldInfo))
        {
            $data=array();
            $data['itemid']=$itemid;
            $data['store_id']=$storeid;
            $ret=$this->insert($data);
            if(empty($ret)){
                Log::record("add item error insert",'zyx');
                return SYSTEM_ERROR;
            }
        }

        if($addorDel){
            $ret=$this->where(['itemid'=>$itemid,'store_id'=>$storeid])->setInc($key,$num);
        }else{
            $ret=$this->where(['itemid'=>$itemid,'store_id'=>$storeid])->setDec($key,$num);
        }

        if(empty($ret)){
            Log::record("add item error",'zyx');
            return SYSTEM_ERROR;
        }
        return SUCCESS;
    }

    public  function  exportNameProcess($name,$data)
    {
        return $data;
    }



}