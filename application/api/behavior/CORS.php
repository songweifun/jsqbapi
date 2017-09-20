<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/8/2
 * Time: 下午10:53
 */

namespace app\api\behavior;
use think\Response;

class CORS
{
    public function appInit(&$params)
    {
//        $domain=request()->domain();
//        $origin = isset($domain)? $domain : '';

//        $allow_origin = array(
//            'http://localhost:4201',
//            'http://en.jselib.findplus.cn',
//            'http://192.168.1.144'
//        );

//        if(in_array($origin, $allow_origin)){
//            header('Access-Control-Allow-Origin:'.$origin);
//            header('Access-Control-Allow-Credentials:true');
//            header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
//            header('Access-Control-Allow-Methods: POST,GET');
//        }
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Credentials:true');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept,orderId,id");
        header('Access-Control-Allow-Methods: POST,GET,DELETE');
        if(request()->isOptions()){
            exit();
        }
    }

}