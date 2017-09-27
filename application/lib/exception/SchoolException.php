<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/25
 * Time: 下午2:04
 */

namespace app\lib\exception;


class SchoolException extends BaseException
{
    public $code = 404;
    public $msg = '成员馆不存在D';
    public $errorCode = 50000;

}