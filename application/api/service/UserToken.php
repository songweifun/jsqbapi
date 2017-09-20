<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/7/23
 * Time: 上午11:24
 */

namespace app\api\service;
use app\api\model\SchoolUser;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use think\Exception;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;


class UserToken extends Token
{

    protected $code;
    protected $wxLoginUrl;
    protected $wxAppID;
    protected $wxAppSecret;
    //构造函数传入code
    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    //这个暂时不用 因为存的也是id不如直接拿id请求
    public function getCode($uid){

        $user=SchoolUser::check($uid);
        if(!$user)
        {
            throw new TokenException([
                'msg' => '授权失败',
                'errorCode' => 10004
            ]);
        }
        else{
            $uid = $user->id;
            $values = [
                'uid' => $uid
            ];
            $code = $this->saveToCache($values);
            return $code;
        }


    }

    public function get(){

        //请求获得openid 封装curl方法
        //查找user表中是否有openid的用户没有则添加一个
        //生成令牌
        //组装缓存数据并且生成缓存  缓存 key 令牌 value wxresult &&uid &&scope
        //将令牌返回客户端
        ///$result = curl_get($this->wxLoginUrl);
        //$wxResult=json_decode($result,true);
        //$wxResult=['openid'=>'111111'];
        $findResult=SchoolUser::check($this->code);
        if(empty($findResult)){
            //返回空
            throw new Exception('获取发现系统前台用户信息时异常，有可能是服务器间通信错误');
        }else{
            //放回非空但是有errcode
//            $loginFail = array_key_exists('errcode', $wxResult);
//            if ($loginFail) {
//                $this->processLoginError($wxResult);
//            }
//            else {
//                return $this->grantToken($wxResult);
//            }
            return $this->grantToken($findResult);
        }
    }

    //生成令牌 同时写入 key为令牌 value为 wxresult && uid &&scope的缓存
    private function grantToken($findResult){

        $findid = $findResult['id'];
        $user = UserModel::getByOpenID($findid);
        if (!$user)
            // 借助微信的openid作为用户标识
            // 但在系统中的相关查询还是使用自己的uid
        {
            $uid = $this->newUser($findResult);//同步发现的数据到本地
        }
        else {
            $uid = $user->id;
        }
        //value
        $cacheValue=$this->prepareCachedValue($findResult, $uid);
        $token = $this->saveToCache($cacheValue);
        return $token;
        //$key

    }
    //写入缓存 返回token
    private function saveTocache($findResult){
        $key = self::generateToken();
        $value = json_encode($findResult);
        $expire_in = config('setting.token_expire_in');
        //令牌过期时间定义为缓存时间
        $result = cache($key, $value, $expire_in);

        if (!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key; //保存到缓存的同时返回这个token

    }

    //组装缓存值value
    private function prepareCachedValue($findResult, $uid)
    {
        $cachedValue = $findResult;
        $cachedValue['uid'] = $uid; //此uid用于修改 方式串了 通过令牌拿到Uid
        //16为app用户 32we
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    // 创建新用户
    private function newUser($findResult)
    {
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        $user = UserModel::create(
            [
                'findid' => $findResult['id'],
                'name' => $findResult['user_name'],
                'email' => $findResult['user_email'],
                'school' => $findResult['school_id'],
                'tel' => $findResult['user_tel'],
                'sex' => $findResult['sex'],
                'name_en' => $findResult['user_name_en'],
            ]);
        return $user->id;
    }

    //有errcode 异常处理
    private function processLoginError($wxResult){
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }



//    public function creteToken(){
//        //生成32位的token
//
//    }

}