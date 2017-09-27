<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/7/24
 * Time: 下午10:22
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Text;
use app\api\model\User;
use app\api\service\Send;
use app\api\service\UserToken;
use app\api\validate\IdMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\api\model\Send as SendModel;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;
use think\Request;
use app\api\service\Send as SendService;
use app\api\service\Text as TextService;
use app\api\service\System as SystemService;

class Order extends BaseController
{

    //客户端调用接口提交订单的详细信息
    //检查库存量 如果有库存则则将订单信息写入表中
    // 如果有库存则告诉用户下单成功 可以支付
    //调用 api 支付接口进行支付
    //再次检查库存量
    //如果有库存 服务器就可以调用微信接口支付
    //支付成功
    //再次检查库存
    //成功 扣除库存


    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'], //用户专用
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser'], //管理员和用户都可以有
        'checkSuperScope' => ['only' => 'delivery,getSummary,sendOrderHandler']  //只有管理员可以有
    ];





    /**
     * 下单
     * @url /order
     * @HTTP POST
     */
    public function placeOrder($title,$doi,$an,$dbid){
          //print_r($products = input('post.orderInfo/a'));die;
        //echo $products=input('post');die;
        //(new OrderPlace())->goCheck();
        //$products = input('post.products/a');
        //return $title."_".$doi;

        $uid=UserToken::getCurrentUid();
        $userInfo=User::getById($uid);

        $requestIp=request()->ip();
        $system=new SystemService($requestIp);
        $system->checkIp();


        if(OrderModel::where('order_from','=',$uid)->where('doi','=',$doi)->where('an','=',$an)->where('status','=',0)->find()){
            throw new OrderException([
                'msg' => '请求已发送，请耐心等待',
                'errorCode' => 20001,
                'code' => 400
            ]);
        }


        $order=OrderModel::create(
            [
                'order_from' => $uid,
                'doi' => $doi,
                'an' => $an,
                'dbid' => $dbid,
                'title' => urldecode($title),
                'requestip' => $requestIp,
                'status'=>2 //等待链接回调的状态此链接为2时链接不发到前台
            ]);


        if(!$order){
            throw new OrderException([
                'msg'=>'下单失败'
            ]);


        }else{
           return json(['order_id'=>$order->id]);
        }


        //  return $success->id;
        //$orderSevice=new OrderService();
        //$status = $orderSevice->place($uid, $title,$doi);
        //return json($status);

    }


    public function asyncUpadateUrl($oid){

        $text=new TextService();
        $text->insertOriginalUrl($oid);

//        $orderInfo=OrderModel::where('id','=',$oid)->find();
//
//        $unique=md5($orderInfo['title'].$orderInfo['an'].$orderInfo['dbid']);//确定文章的唯一号
//
//
//        $textArr=Text::where('unique','=',$unique)->where('uploader','=',0)->select();//唯一号标识的且被系统采纳的 原库的会被自动采纳 uploader为0说明是系统原文
//
//
//        if($textArr->isEmpty()){
//            //如果原库不存在
//            $send=new SendService($oid);
//            $url=$send->getUrl();
//            if($url){
//                //如果原库存在添加到全文表并标记为被系统采纳
//                $text=Text::create(
//                    [
//                        'url'=>$url,
//                        'unique'=>$unique,
//                        'uploader'=>0,//代表原库
//                        //isAdopt默认为1
//
//                    ]
//                );
//                if(!$text){
//                    throw  new OrderException([
//                        'msg'=>'添加原库连接失败'
//                    ]);
//                }else{
//
//                    $success=OrderModel::where('id','=',$oid)->update([
//                        //'url'=>$url, //推送订单的时候计算
//                        'status'=>0 //这个回调中链接已经确定可以发送到前台抢单
//                    ]);
//                    if(!$success){
//                        throw new OrderException([
//                            'msg'=>'更新失败'
//                        ]);
//                    }else{
//                        throw new SuccessMessage([
//                            'msg'=>'更新成功'
//                        ]);
//                    }
//
//                }
//
//            }
//
//
//        }//if end













    }

    /**
     * @param $id
     * @return $this
     * @throws OrderException
     */
    public function getDetail($id){
        (new IdMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail)
        {
            throw new OrderException();
        }
        $result=$orderDetail->hidden(['prepay_id'])->toArray();
        $result['snap_items']=json_decode($result['snap_items'],true);
        $result['snap_address']=json_decode($result['snap_address'],true);
        return json($result);

    }

    /**
     * 获取最新的订单用于推送
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getSummaryByNew($page=1,$size=15){
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByNew( $page, $size);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }

        return json([
            'current_page' => $pagingOrders->currentPage(),
            'data' => $pagingOrders
        ]);
    }


    /**
     * 根据用户id分页获取订单列表（简要信息）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($pageNumber = 1, $pageSize = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = UserToken::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }
        $collection = collection($pagingOrders->items());
        $data = $collection->hidden(['snap_items', 'snap_address'])
            ->toArray();
//        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
//            ->toArray();
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);

    }


    /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($pageNumber=1, $pageSize = 20){
        (new PagingParameter())->goCheck();
//        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByPage($pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);
    }


    public function delivery($id){
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if($success){
            throw new SuccessMessage();
        }
    }


    /**
     * 发送论文传递
     */
    public function sendOrderHandler($id){
        $send=new Send($id);
        //首先添加一个发送订单

        //其次更改订单的状态

        $success= $send->send();

        if(!$success){
            throw new OrderException([
                'msg'=>'传递失败'
            ]);
        }else{
            throw new SuccessMessage();
        }
    }

    /**
     * 获得指定用户的已经被传递的订单
     */
    public function getSendOrderByUserNew($pageNumber=1,$pageSize=10){

        $send=new SendModel();
        $pagingOrders=$send->getSendOrderByUser($pageNumber,$pageSize);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }
        $data = $pagingOrders->hidden()
            ->toArray();
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);

    }


    public function getSendOrderByUserAll($pageNumber=1,$pageSize=20){
        $order=new OrderModel();
        $pagingOrders=$order->getSendOrderByUserAll($pageNumber,$pageSize);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }
        $data = $pagingOrders->hidden()
            ->toArray();
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);

    }

}