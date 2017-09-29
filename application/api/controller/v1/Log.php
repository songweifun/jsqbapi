<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/28
 * Time: 下午2:41
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Token;
use app\api\model\Log as LogModel;

class Log extends BaseController
{
    public function recordLoginLog(){
        $user=Token::getCurrentUid();
        $ip=request()->ip();
        $log=new LogModel();
        $log->log(1,$user,$ip,'登录');

    }

    public function recordLogoutLog(){
        $user=Token::getCurrentUid();
        $ip=request()->ip();
        $log=new LogModel();
        $log->log(2,$user,$ip,'退出登录');

    }
    public function getEntryLog($pageNumber=1, $pageSize=20){
        $usermodel=new LogModel();
        $pagingOrders = $usermodel->getEntryLog( $pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }
        $collection = collection($pagingOrders->items());
        $data = $collection->hidden()
            ->toArray();

        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);
    }

    public function getOperateLog($pageNumber=1, $pageSize=20){
        $usermodel=new LogModel();
        $pagingOrders = $usermodel->getOperateLog( $pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }
        $collection = collection($pagingOrders->items());
        $data = $collection->hidden()
            ->toArray();

        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);
    }

}