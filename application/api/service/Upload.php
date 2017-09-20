<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/9
 * Time: 下午1:15
 */

namespace app\api\service;



class Upload
{
    public function upload(){
        $file = request()->file('pdf');
        $uploader=Token::getCurrentTokenVar('uid');
        //echo $uploader;
        //echo request()->header('orderId');
        $oid=request()->header('orderId');

        $info = $file->validate(['size'=>300000])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
//            echo $info->getExtension();
//            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//            echo $info->getSaveName();
//            // 输出 42a79759f284b767dfcb2a0197904287.jpg
//            echo $info->getFilename();

            $url=request()->domain().'/'.request()->root().'/uploads/'.$info->getSaveName();

            $text=new Text();
            $text->insertUploadUrl($oid,$url,$uploader);


        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }

    }

}