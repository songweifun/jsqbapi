<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/19
 * Time: 上午11:34
 */

namespace app\api\service;

use app\api\model\Map as MapModel;
use app\lib\exception\OrderException;
use app\api\service\Order as OrderService;


class System
{
    private $ipArr=[];
    private $ip;

    public function __construct($ip)
    {
        $this->ip=$ip;
        $this->ipArr=$this->findIpInMap();

    }

    public function checkIp(){
        if(!$this->validateIsOpen()){
            throw new OrderException([
                'msg'=>'此IP没有开通此项服务',
                'code'=>401,
                'errorCode'=>20005
            ]);
        }
        if(!$this->validateWhite()){
            throw new OrderException([
                'msg'=>'此IP没有开通此项服务或被加入黑名单',
                'code'=>401,
                'errorCode'=>20002
            ]);
        }

        if($this->validateBlack()){
            throw new OrderException([
                'msg'=>'ip被加入黑名单',
                'code'=>401,
                'errorCode'=>20003
            ]);
        }

        if($this->validateExpire()){
            throw new OrderException([
                'msg'=>'服务已过期！请联系提供商!',
                'code'=>401,
                'errorCode'=>20004
            ]);
        }
        if($this->validateMonthLimit()){
            throw new OrderException([
                'msg'=>'请求数量已经超过了月最大请求数量',
                'code'=>401,
                'errorCode'=>20006
            ]);
        }

        if($this->validateDayLimit()){
            throw new OrderException([
                'msg'=>'请求数量已经超过了日最大请求数量',
                'code'=>401,
                'errorCode'=>20007
            ]);
        }
    }
    /**
     * 开启验证
     * @param $ip
     */
    public function validateIsOpen(){
        $result=false;
        foreach ($this->ipArr as $k=>$v){
            if($v['is_open']==1){
                $result=true;
            }
        }


        return $result;



    }
    /**
     * ip白名单验证
     * @param $ip
     */
    public function validateWhite(){

        $result=false;
        foreach ($this->ipArr as $k=>$v){
            if($v['is_forbid']==0){
                $result=true;
            }
        }


        return $result;

    }

    /**
     * ip黑名单验证
     * @param $ip
     */
    public function validateBlack(){

        $result=false;
        foreach ($this->ipArr as $k=>$v){
            if($v['is_forbid']==1){
                $result=true;
            }
        }


        return $result;

    }

    /**
     * 开通时间限制
     */
    public function validateExpire(){
        //true表示过期
        $result=false;
        foreach ($this->ipArr as $k=>$v){
            if(time()>$v['expire']){
                $result=true;
            }
        }
        return $result;


    }


    public function validateMonthLimit(){
        $ipArr=$this->findIpInMapWithoutBlack();
        $result=false;
        //true就超过了限制
        $orderService=new OrderService();
        $requestNumber=$orderService->getIpMapSumaryOrdersByMonth($ipArr);
        foreach ($this->ipArr as $k =>$v){
            if($requestNumber>=$v['monthlimit']){
                $result=true;
            }
        }

        return $result;


    }

    public function validateDayLimit(){
        $ipArr=$this->findIpInMapWithoutBlack();
        $result=false;
        //true就超过了限制
        $orderService=new OrderService();
        $requestNumber=$orderService->getIpMapSumaryOrdersByDay($ipArr);
        foreach ($this->ipArr as $k =>$v){
            if($requestNumber>=$v['daylimit']){
                $result=true;
            }
        }

        return $result;

    }

    /**
     * 获得指定ip所在的映射 并按照添加时间先后顺序排序
     * @param $ip
     * @return array
     */
    public function findIpInMap(){
        $ip=get_iplong($this->ip);
        $ipMaps=MapModel::order('create_time desc')->select();
        $ipArr=array();
        foreach ($ipMaps as $k=>$v){
            $ip_start=get_iplong($v['ip_start']);
            $ip_end=get_iplong($v['ip_end']);

            if($ip>=$ip_start && $ip <=$ip_end){
                $ipArr[]=$v;
            }
        }
        return $ipArr;
    }


    public function findIpInMapWithoutBlack(){
        $ip=get_iplong($this->ip);
        $ipMaps=MapModel::where('is_forbid','=',0)->order('create_time desc')->select();
        $ipArr=array();
        foreach ($ipMaps as $k=>$v){
            $ip_start=get_iplong($v['ip_start']);
            $ip_end=get_iplong($v['ip_end']);

            if($ip>=$ip_start && $ip <=$ip_end){
                $ipArr[]=$v;
            }
        }
        return $ipArr;

    }



}