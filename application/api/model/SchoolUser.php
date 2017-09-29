<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/5
 * Time: 下午9:37
 */

//find上前台用户表

namespace app\api\model;


class SchoolUser extends BaseModel
{
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => '42.96.147.165',
        // 数据库名
        'database'    => 'findplus_user',
        // 数据库用户名
        'username'    => 'baohefan',
        // 数据库密码
        'password'    => 'swf@!(*&01',
        // 数据库连接端口
        'hostport'    => '',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => '',
    ];

    public static function check($uid)
    {
        $user = self::where('id','=',$uid)
            ->find();
        return $user;

    }

    public function getAllUser($keyword,$page,$size){
        //echo $keyword;die;
        if(!$keyword){
            $pagingData = self::order('id desc')
                ->paginate($size, false, ['page' => $page]);
        }else{
            $pagingData = self::where('user_email','like',"%$keyword%")->order('id desc')
                ->paginate($size, false, ['page' => $page]);
        }

        return $pagingData ;
    }

}