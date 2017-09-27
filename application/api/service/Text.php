<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/9
 * Time: 下午1:05
 */

namespace app\api\service;
use app\api\model\Order as OrderModel;
use app\api\model\Text as TextModel;
use app\api\service\Send as SendService;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;


class Text
{

    public function getOrderUniqueKey($oid){
        $orderInfo=OrderModel::where('id','=',$oid)->find();

        $unique=md5($orderInfo['title'].$orderInfo['an'].$orderInfo['dbid']);//确定文章的唯一号
        return $unique;
    }

    public function insertOriginalUrl($oid){
        $unique=$this->getOrderUniqueKey($oid);
        //echo $unique;die;
        $textArr=TextModel::where('unique','=',$unique)->where('uploader','=',0)->select();//唯一号标识的且被系统采纳的 原库的会被自动采纳 uploader为0说明是系统原文


        if($textArr->isEmpty()){
            //echo 1111;die;
            //echo $oid;die;
            //如果原库不存在
            $send=new SendService($oid);
            $url=$send->getUrl();
            if($url){
                //如果原库存在添加到全文表并标记为被系统采纳
                $text=TextModel::create(
                    [
                        'url'=>$url,
                        'unique'=>$unique,
                        'uploader'=>0,//代表原库
                        //isAdopt默认为1

                    ]
                );
                if(!$text){
                    throw  new OrderException([
                        'msg'=>'添加原库连接失败'
                    ]);
                }else{

                    $success=OrderModel::where('id','=',$oid)->update([
                        'url'=>$url, //推送订单的时候计算
                        'status'=>0 //这个回调中链接已经确定可以发送到前台抢单
                    ]);
                    if(!$success){
                        throw new OrderException([
                            'msg'=>'更新失败'
                        ]);
                    }else{
                        throw new SuccessMessage([
                            'msg'=>'更新成功'
                        ]);
                    }

                }

            }else{
                //这个是后来加上的

                $success=OrderModel::where('id','=',$oid)->update([
                    'url'=>$url, //推送订单的时候计算
                    'status'=>0 //这个回调中链接已经确定可以发送到前台抢单
                ]);
                if(!$success){
                    throw new OrderException([
                        'msg'=>'更新失败'
                    ]);
                }else{
                    throw new SuccessMessage([
                        'msg'=>'更新成功'
                    ]);
                }

            }


        }//if end

    }

    public function insertUploadUrl($oid,$url,$uploader){
        $unique=$this->getOrderUniqueKey($oid);
        $text=TextModel::create(
            [
                'url'=>$url,
                'unique'=>$unique,
                'uploader'=>$uploader,//代表原库
                'isAdopt'=>0//isAdopt默认为1

            ]
        );

        if(!$text){
            throw new OrderException([
               'msg'=>'添加上传链接失败'
            ]);
        }else{
            throw new SuccessMessage([
               'msg'=>'添加上传链接成功'
            ]);
        }

    }

}