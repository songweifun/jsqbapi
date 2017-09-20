<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/7
 * Time: 下午1:49
 */

namespace app\api\service;


use app\api\model\Order;
use app\api\model\Send as SendModel;
use app\lib\exception\OrderException;

class Send
{
    protected $orderId;
    public function __construct($orderId)
    {
        $this->orderId=$orderId;

    }

    public function send(){
        $uid = UserToken::getCurrentUid();//sender_id
        //$id order_id
        $orderInfo= $this->getOrderInfo();

        //$url=$this->getUrl();
        $send=SendModel::create(
            [
                //'url'  =>  $url,
                'order_id' =>  $this->orderId,
                'sender_id' =>  $uid,
                'send_to'=>$orderInfo['order_from']
            ]
        );

        if(!$send){
            throw new OrderException(
                [
                  'msg'=>'订单发送失败'
                ]
            );
        }

        $success=Order::where('id','=',$this->orderId)->update(['status'=>1]);

        return $success;



    }


    public function getUrl(){
        //"http://202.121.183.42:9000/?doi='.$doi.'&title='.urlencode(strip_tags($title)).'&aid='.$params['an'].$params['dbid'].'"
        $orderInfo= $this->getOrderInfo();

        $requestUrl="http://202.121.183.42:9000/?doi=".$orderInfo['doi'].'&title='.urlencode($orderInfo['title']).'&aid='.$orderInfo['an'].$orderInfo['dbid'];
        //return $requestUrl;

        $respose=curl_get($requestUrl);

        if(preg_match('/no pdf!/',$respose)){
            //如果不存在返回空

            return '';
        }else{
            //如果存在返回拼接的地址
            return $requestUrl;
        }

        //return $respose;


    }


    private function getOrderInfo(){
        return Order::where('id','=',$this->orderId)->find();
    }




}