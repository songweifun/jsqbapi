<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/28
 * Time: 上午11:39
 */

namespace app\api\model;


class qtUserBase extends BaseModel
{
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => '42.96.147.165',
        // 数据库名
        'database'    => 'findplus',
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


    public function getAllSchoolMessage(){
        $result=self::field(['user_id','user_name'])->select();
        return $result;
    }


}