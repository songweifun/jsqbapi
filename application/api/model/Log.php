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

}