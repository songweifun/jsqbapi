<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/8/2
 * Time: 下午8:02
 */

namespace app\api\service;


use app\api\model\Admin;
use app\lib\exception\TokenException;

class AppToken extends Token
{
    public function get($ac, $se){
        $app = Admin::check($ac, $se);
        if(!$app)
        {
            throw new TokenException([
                'msg' => '授权失败',
                'errorCode' => 10004
            ]);
        }
        else{
            $scope = $app->scope;
            $uid = $app->id;
            $values = [
                'scope' => $scope,
                'uid' => $uid
            ];
            $token = $this->saveToCache($values);
            return $token;
        }

    }

    private function saveToCache($values){
        $token = self::generateToken();
        $expire_in = config('setting.token_expire_in');
        $result = cache($token, json_encode($values), $expire_in);
        if(!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }

}