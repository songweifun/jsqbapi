<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/1
 * Time: 下午1:32
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\exception\LibraryException;
use app\lib\exception\ParmeterException;
use app\lib\exception\SuccessMessage;
use app\api\model\School as SchoolModel;

class Admin extends BaseModel
{
    public function getSchoolIdAttr($value)
    {
        return SchoolModel::get($value);
    }
    public function getLibraries(){
        return self::all();

    }
    public static function check($ac, $se)
    {
        $app = self::where('app_id','=',$ac)
            ->where('app_secret', '=',$se)
            ->find();
        return $app;

    }

    public function createNewLibray(){
        $postData=input('post.data/a');

        //print_r($postData);die;

        if(self::where('app_id','=',$postData['app_id'])->count()>0){
            throw new LibraryException([
               'msg'=>'app_id已经存在',
                'code'=>201
            ]);
        }

        if($postData['secret']['app_secret']!==$postData['secret']['c_app_secret']){
            throw new ParmeterException([
               'msg'=>'两次密码不一致'
            ]);
        }
        $uid=Token::getCurrentUid();
        $admin=self::create([
            'app_id'=>$postData['app_id'],
            'app_secret'=>$postData['secret']['app_secret'],
            'school_id'=>$postData['school_id'],
            'is_open'=>$postData['isOpen'],
            'adder'=>$uid,
            'scope'=>$postData['scope'],
        ]);

        throw new SuccessMessage([
            'msg'=>'添加成功',
            //'errorCode'=>1
        ]);
    }


    public function updateLibray(){
        $postData=input('post.data/a');

        if($postData['secret']['app_secret']!==$postData['secret']['c_app_secret']){
            throw new ParmeterException([
                'msg'=>'两次密码不一致'
            ]);
        }
        $id=input('post.id');
        $dataArr=[
            'app_secret'=>$postData['secret']['app_secret'],
            'school_id'=>$postData['school_id'],
            'is_open'=>$postData['isOpen'],
            'scope'=>$postData['scope'],
            'app_id'=>$postData['app_id'],
        ];
        $success=self::where('id','=',$id)->update($dataArr);
        if(!$success){
            throw new LibraryException([
                'msg'=>'成员馆更新失败',
                'errorCode'=>'40002',
                'code'=>'201'
            ]);

        }else{
            throw new SuccessMessage();
        }
    }

}