<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/7/26
 * Time: 下午10:32
 */

namespace app\api\model;


use app\api\service\Text;
use app\api\model\Text as TextModel;
use app\api\service\Token;

class Order extends BaseModel
{
    private $uploader;
    protected $hidden=['user_id','delete_time','update_time'];
    protected $autoWriteTimestamp=true; //自动写入时间戳
    //protected $createTime='';
    //protected $updateTime
    //获取器有无原库原文
    public function getIsHaveAttr($value,$data)
    {
        $text=new Text();
        $unique=$text->getOrderUniqueKey($data['id']);
        $this->uploader=Token::getCurrentTokenVar('uid');
        //$count=TextModel::where('unique','=',$unique)->count();
        $count=TextModel::where('unique','=',$unique)
            ->where(function ($query) {
            $query->where('uploader', '=', 0)->whereOr('uploader', '=', $this->uploader);
        })->count();

       return $count;
    }


    public function getRequestMapAttr($value,$data)
    {
        $map=new Map();
        $requstName=$map->getRequestNameByIp($data['requestip']);
        return $requstName;
    }

    public function getFromNameAttr($value,$data)
    {
        $user=new User();
        $userContent=$user->find($data['order_from']);
        return $userContent['nickname']?$userContent['nickname']:$userContent['email'];
    }

//    public function getStatusTextAttr($value,$data)
//    {
//        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
//        return $status[$data['status']];
//    }


    public function send()
    {
        return $this->hasOne('Send','order_id','id',[],'RIGHT');
    }


    public static function getSummaryByUser($uid, $page=1, $size=15)
    {
        $ids= Send::where('sender_id','=',$uid)->column('order_id');
        $pagingData = self::with(['send'])->where('id','in',$ids)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }

//    public function getSenderOrderIds($uid){
//        return Send::where('sender_id','=',$uid)->getAttr('order_id');
//    }


    public static function getSummaryByPage($page=1, $size=20){
        $ids= Send::column('order_id');
        $pagingData = self::with(['send'])->where('id','in',$ids)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }


    public static function getSummaryByNew($page=1, $size=20){
        $pagingData=self::where('status','=',0)
            ->order('create_time desc')
            ->paginate($size,true,['page'=>$page]);
        return $pagingData ;
    }


    //获得用户的所有申请
    public function getSendOrderByUserAll($page,$size){
        $uid=Token::getCurrentUid();
        $pagingData = self::with(['send'])->where('order_from','=',$uid)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }


}