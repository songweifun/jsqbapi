<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/14
 * Time: 下午10:07
 */

namespace app\lib\exception;


class IpMapException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查ID';
    public $errorCode = 30000;

}