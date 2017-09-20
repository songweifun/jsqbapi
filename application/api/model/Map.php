<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/14
 * Time: 下午1:27
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\exception\IpMapException;
use app\lib\exception\SuccessMessage;

class Map extends BaseModel
{
    protected $autoWriteTimestamp=true; //自动写入时间戳


    public function createNew(){
        $postData=input('post.data/a');
        $is_forbid=input('post.is_forbid');
        $time=$postData['time'];
        $timeStamp=mktime(0,0,0,$time['month'],$time['day'],$time['year']);
        $uid=Token::getCurrentUid();
        $map=Map::create([
            'ip_start'=>$postData['ipRange']['start'],
            'ip_end'=>$postData['ipRange']['end'],
            'map_name'=>$postData['mapName'],
            'is_open'=>$postData['isOpen'],
            'adder'=>$uid,
            'is_forbid'=>$is_forbid,
            'expire'=>$timeStamp,
            'daylimit'=>$postData['daylimit'],
            'monthlimit'=>$postData['monthlimit']
        ]);

        throw new SuccessMessage([
            'msg'=>'添加成功',
            //'errorCode'=>1
        ]);
    }


    public function getSumary($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;

    }

    public function updateIpMap(){
        $postData=input('post.data/a');
        $time=$postData['time'];
        $timeStamp=mktime(0,0,0,$time['month'],$time['day'],$time['year']);

        $id=input('post.id');
        $dataArr=[
            'ip_start'=>$postData['ipRange']['start'],
            'ip_end'=>$postData['ipRange']['end'],
            'map_name'=>$postData['mapName'],
            'is_open'=>$postData['isOpen'],
            'expire'=>$timeStamp,
            'daylimit'=>$postData['daylimit'],
            'monthlimit'=>$postData['monthlimit']
        ];
        $success=self::where('id','=',$id)->update($dataArr);
        if(!$success){
            throw new IpMapException([
               'msg'=>'IP映射更新失败',
               'errorCode'=>'30001',
                'code'=>'201'
            ]);

        }else{
            throw new SuccessMessage();
        }
    }

    public function getIpWhite($page,$size){
        $pagingData = self::where('is_forbid','=',0)->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }
    public function getIpBlack($page,$size){
        $pagingData = self::where('is_forbid','=',1)->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }

    public function getRequestNameByIp($ip){
        $ip=get_iplong($ip);
        $ipMaps=self::where('is_open','=',1)->select();
        foreach ($ipMaps as $k=>$v){
            $mapName='';
            $ip_start=get_iplong($v['ip_start']);
            $ip_end=get_iplong($v['ip_end']);

            if($ip>=$ip_start && $ip <=$ip_end){
                $mapName=$v['map_name'];
            }else{
                $mapName='未知';
            }

        }
        return $mapName;
    }

}