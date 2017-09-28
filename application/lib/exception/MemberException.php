<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/27
 * Time: 下午7:03
 */

namespace app\lib\exception;


class MemberException extends BaseException
{
    public $code = 404;
    public $msg = '奇台用户不存在';
    public $errorCode = 40000;

}