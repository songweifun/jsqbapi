<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/28
 * Time: 下午3:16
 */

namespace app\lib\exception;


class LogException extends BaseException
{
    public $code = 404;
    public $msg = '日志不存在';
    public $errorCode = 70000;

}