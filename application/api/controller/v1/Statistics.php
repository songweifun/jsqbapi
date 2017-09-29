<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/25
 * Time: 下午6:30
 */

namespace app\api\controller\v1;
use think\Db;



class Statistics
{
    public function getOrderStatisticsBySchool(){
        $sql="select b.school_id,count(*) as value,c.name from jsqb_send as a left JOIN jsqb_admin as b on a.sender_id=b.id left join jsqb_school as c on b.school_id=c.id  GROUP BY school_id";
        $result=Db::query($sql);
        return json($result);


    }


    public function getResourceStatisticsBySchool(){
        $sql="select b.school_id,count(*) as value,c.name from jsqb_text as a left JOIN jsqb_admin as b on a.uploader=b.id left join jsqb_school as c on b.school_id=c.id WHERE a.uploader!=0 GROUP BY school_id";
        $result=Db::query($sql);
        return json($result);
    }

    public function getRequstStatisticsByIpTop10(){
        $sql="select requestip as name,count(*) as value from jsqb_order GROUP BY requestip ORDER BY COUNT(*) DESC limit 10";
        $result=Db::query($sql);
        return json($result);
    }

    public function getRequestStatisticsByMapNameTop10(){
        $sql="select c.map_name as name,COUNT(*) as value from jsqb_order as a LEFT JOIN jsqb_map_spite as b on a.requestip=b.ip LEFT JOIN jsqb_map as c on b.map_id=c.id GROUP BY c.id ORDER BY COUNT(*) DESC limit 10";
        $result=Db::query($sql);
        foreach ($result as $k=>$v){
            if($v['name']==null){
                $result[$k]['name']='未知';
            }
        }
        return json($result);
    }


}