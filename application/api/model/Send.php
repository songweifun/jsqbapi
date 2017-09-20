<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/7
 * Time: 下午1:46
 */

namespace app\api\model;


use app\api\service\Text as TextSevice;
use app\api\service\Token;
use think\model\Merge;
use app\api\model\Text as TextModel;

class Send extends BaseModel
{
    protected $autoWriteTimestamp=true; //自动写入时间戳

    private $uid;


//    // 定义关联模型列表
//    protected  $relationModel = ['Order'];
//    // 定义关联外键
//    protected $fk = 'order_id';
//    protected $mapFields = [
//        // 为混淆字段定义映射
//        'id'        =>  'Send.id',
//        'order_id' =>  'Order.id',
//    ];




    public function order()
    {
        return $this->belongsTo('Order','order_id','id')->bind([
//            'email',
//            'truename'	=> 'nickname',
//            'profile_id'  => 'id',
                //'order_from'=>'order_from'
        ]);
            //->hasWhere('order_from','=',$this->uid);
    }

    public function getUrlAttr($value,$data)
    {
        $orderId=array_key_exists('order',$data)?$data['order']['id']:'';
        $text=new TextModel();
        $texSevice=new TextSevice();
        $unique=$texSevice->getOrderUniqueKey($orderId);
        $result=$text->where('unique','=',$unique)->select()->toArray();
        //$userContent=$user->find($data['order_from']);
//        return $userContent['nickname']?$userContent['nickname']:$userContent['email'];
        return $result;
    }

    //获得后台管理员的所有传递订单
    public static  function getSummaryByUser($uid, $page, $size){

        $pagingData = self::with(['order'])
            ->where('sender_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;


    }
    //获得后台管理员传递给指定用户的所有订单
    public function getSendOrderByUser($page,$size){
        $this->uid=$uid=Token::getCurrentUid();
        $pagingData = self::with([
            'order'=>function($query){
                $query->where('order_from','=',$this->uid);
            }
        ])->where('send_to','=',$this->uid)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
        return $uid;
    }


}