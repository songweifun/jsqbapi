<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/27
 * Time: 下午5:14
 */

namespace app\api\controller\v1;

use app\api\model\qtUserBase;
use app\api\model\SchoolUser as SchoolUserModel;


use app\api\controller\BaseController;
use app\lib\exception\MemberException;
use app\lib\exception\SuccessMessage;

class Member extends BaseController
{
    public function getAllUserByKeyword($keyword='',$pageNumber = 1, $pageSize = 15){

    }
    public function getAllUser($keyword='',$pageNumber = 1, $pageSize = 15){

        $usermodel=new SchoolUserModel();
        $pagingOrders = $usermodel->getAllUser( $keyword,$pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return json([
                'current_page' => $pagingOrders->currentPage(),
                'rows' => [],
                'total'=>0,
                'page_size'=>$pageSize
            ]);
        }
        $collection = collection($pagingOrders->items());
        $data = $collection->hidden()
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

    public function delteMember(){
        $id= request()->header('id');
        $success=SchoolUserModel::destroy($id);
        if(!$success){
            throw new MemberException([
                'msg'=>'删除失败',
                'errorCode'=>'60003'
            ]);
        }else{
            throw new SuccessMessage([
                'msg'=>'删除成功',
                //'code'=>204
            ]);
        }
    }

    public function getDetailOneMember($id){
        $user=new SchoolUserModel();
        $result=$user->get($id);
        return $result;
    }


    public function updateMember(){
        $postData=input('post.data/a');

        if($postData['secret']['app_secret']!==$postData['secret']['c_app_secret']){
            throw new ParmeterException([
                'msg'=>'两次密码不一致'
            ]);
        }
        $id=input('post.id');
        $dataArr=[
            'user_password'=>$postData['secret']['app_secret'],
            //'school_id'=>$postData['school_id'],
            'is_open'=>$postData['isOpen'],
            'scope'=>$postData['scope'],
            'user_email'=>$postData['user_email'],
        ];
        $success=SchoolUserModel::where('id','=',$id)->update($dataArr);
        if(!$success){
            throw new MemberException([
                'msg'=>'成员馆更新失败',
                'errorCode'=>'60002',
                'code'=>'201'
            ]);

        }else{
            throw new SuccessMessage();
        }

    }

    public function getAllSchoolMessage(){
        $schools=new qtUserBase();
        $result=$schools->getAllSchoolMessage();
        return json($result);
    }


    public function updateMemberIsOpen(){
        $member = SchoolUserModel::get(input('post.id'));
        $member->is_open=input('post.status');
        $success=$member->save();

        if(!$success){
            throw new MemberException([
                'msg'=>'映射状态更新失败',
                'errorCode'=>'60001'
            ]);
        }else{
            throw new SuccessMessage([
                'msg'=>'IP映射状态更新成功'
            ]);
        }
    }

}