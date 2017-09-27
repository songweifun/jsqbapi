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

        $this->checkIpMap($postData['ipRange']['start'],$postData['ipRange']['end']);

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



        if($map->id){

            $this->createIpMapSpite($postData['ipRange']['start'],$postData['ipRange']['end'],$map->id);
            throw new SuccessMessage([
                'msg'=>'添加成功',
                //'errorCode'=>1
            ]);

        }else{

            throw new IpMapException([
                'msg'=>'添加失败',
                //'errorCode'=>1
            ]);


        }


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
            MapSpite::where('map_id','=',$id)->delete();//删除分散的ip
            //重新加入分散的id
            $this->checkIpMap($postData['ipRange']['start'],$postData['ipRange']['end']);
            $this->createIpMapSpite($postData['ipRange']['start'],$postData['ipRange']['end'],$id);

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

    public function createIpMapSpite($start,$end,$map_id){
        //把ip一个个的插入spite表
        $ip_start=ip2long($start);
        $ip_end=ip2long($end);

        $ip=$ip_start;
        while($ip>=$ip_start && $ip<=$ip_end){



            MapSpite::create([
                'ip'=>long2ip($ip),
                'map_id'=>$map_id
            ]);

            $ip++;

        }


    }


    public function checkIpMap($start,$end){
        $ip_start=ip2long($start);
        $ip_end=ip2long($end);


        $maxRange=config('ip.ipMapRange');

        if($ip_end-$ip_start>$maxRange){
            throw new IpMapException([
                'code'=>201,
                'errorCode'=>30005,
                'msg'=>"目前ip映射端设为{$maxRange}个,若想添加更大范围请联系管理员修改配置项"
            ]);
        }

        $ip=ip2long($ip_start);
        while($ip>=$ip_start && $ip<$ip_end){
            $ip++;

            if(MapSpite::where('ip','=',long2ip($ip))->find()){
                throw new IpMapException([
                    'code'=>201,
                    'errorCode'=>30004,
                    'msg'=>'此ip段的部分Ip已经映射过,IP段存在交集'
                ]);
            }
        }

    }

}