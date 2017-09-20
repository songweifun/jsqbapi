<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/7/26
 * Time: 下午8:04
 */

namespace app\api\service;
use app\api\model\Order as OrderModel;




class Order
{




    public function getIpMapSumaryOrdersByMonth($ipArr){
        //$monthTime=time()-30*24*60*60;
        $timeArr=getdate();
        $monthTimeStamp=mktime(0,0,0,$timeArr['mon'],0,$timeArr['year']);
        //echo $monthTimeStamp;die;
        $orders=OrderModel::where('create_time','>=',$monthTimeStamp)->select();
        $sum=0;
        foreach ($ipArr as $k=>$v){
            foreach ($orders as $kk=>$vv){
                $ip_start=get_iplong($v['ip_start']);
                $ip_end=get_iplong($v['ip_end']);
                $ip=get_iplong($vv['requestip']);

                if($ip>=$ip_start && $ip <=$ip_end){
                    $sum++;
                }
            }
        }

        return $sum;
    }

    public function getIpMapSumaryOrdersByDay($ipArr){
        $timeArr=getdate();
        $dayTimeStamp=mktime(0,0,0,$timeArr['mon'],$timeArr['mday'],$timeArr['year']);
        $orders=OrderModel::where('create_time','>=',$dayTimeStamp)->select();
        $sum=0;
        foreach ($ipArr as $k=>$v){
            foreach ($orders as $kk=>$vv){
                $ip_start=get_iplong($v['ip_start']);
                $ip_end=get_iplong($v['ip_end']);
                $ip=get_iplong($vv['requestip']);

                if($ip>=$ip_start && $ip <=$ip_end){
                    $sum++;
                }
            }
        }

        return $sum;
    }

}