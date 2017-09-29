<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/28
 * Time: 下午3:10
 */

namespace app\api\model;


use app\lib\exception\LogException;
use app\lib\exception\SuccessMessage;

class Log extends BaseModel
{
    protected $autoWriteTimestamp=true; //自动写入时间戳


    public function getUserAttr($value,$data){
       $user=Admin::get($value)->hidden(['app_secret']);
       return $user;

    }

    public function log($type,$user,$ip,$content){
        $log=self::create(
            [
                'type'=>$type,
                'user'=>$user,
                'ip'=>$ip,
                'content'=>$content
            ]
        );

        if(!$log){
            throw new LogException([
                'msg'=>'日志记录失败',
                'errorCode'=>70001,
                'code'=>201
            ]);
        }else{
            throw new SuccessMessage();
        }

    }


    public function getEntryLog($page,$size){
        $pagingData=self::where('type','in',[1,2])->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }

    public function getOperateLog($page,$size){
        $pagingData=self::where('type','not in',[1,2])->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }

}