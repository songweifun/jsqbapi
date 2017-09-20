<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/19
 * Time: 下午4:47
 */

namespace app\lib\exception;


class LibraryException extends BaseException
{
    public $code = 404;
    public $msg = '成员馆不存在D';
    public $errorCode = 40000;

}