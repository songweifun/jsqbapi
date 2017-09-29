<?php
/**
 * Created by PhpStorm.
 * User: daivd
 * Date: 2017/9/14
 * Time: 上午11:19
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Log;
use app\api\model\Map;
use app\api\model\MapSpite;
use app\api\service\Token;
use app\lib\exception\IpMapException;
use app\lib\exception\LibraryException;
use app\lib\exception\SchoolException;
use app\lib\exception\SuccessMessage;
use app\api\model\Admin as AdminModel;
use app\api\model\School as SchoolModel;

class System extends BaseController
{

    public function getSchools(){
        $result=SchoolModel::all();
        if($result->isEmpty()){
            throw new SchoolException();

        }else{
            return json($result);
        }

    }

    public function getLibrarys(){

        $admin=new AdminModel();
        $result=$admin->getLibraries();
        if($result->isEmpty()){
            throw new LibraryException();

        }else{
            return json($result);
        }
    }

    public function updateLibraryOpen($status,$id){
        $success=AdminModel::where('id','=',$id)->update([
           'is_open'=>$status
        ]);
        if(!$success){
            throw new LibraryException([
                'msg'=>'成员馆状态更新失败',
                'errorCode'=>'40001'
            ]);
        }else{
            throw new SuccessMessage([
                'msg'=>'成员馆状态更新成功'
            ]);
        }
    }


    public function addLibrary(){
        $admin=new AdminModel();
        $admin->createNewLibray();
    }

    public function updateLibrary(){
        $admin=new AdminModel();
        $admin->updateLibray();
    }


    public function getLibraryDetail($id){
        $admin=new AdminModel();
        $result=$admin->get($id);
        return $result;
    }

    public function deleteLibrary(){
        $id= request()->header('id');
        $success=AdminModel::destroy($id);
        if(!$success){
            throw new LibraryException([
                'msg'=>'删除失败',
                'errorCode'=>'40003'
            ]);
        }else{
            throw new SuccessMessage([
                'msg'=>'删除成功',
                //'code'=>204
            ]);
        }

    }

    public function ipMap(){
        //print_r(input('post.data/a'));die;
//        $postData=input('post.data/a');
//        $map=Map::create([
//            'ip_start'=>$postData['ipRange']['start'],
//            'ip_end'=>$postData['ipRange']['end'],
//            'map_name'=>$postData['mapName'],
//            'is_open'=>$postData['isOpen'],
//        ]);
        $map=new Map();
        $map->createNew();
    }


    public function getAllMap($pageNumber=1,$pageSize=20){
        $map=new Map();
        $pagingOrders = $map->getSumary($pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden()
            ->toArray();
        //return json($data);
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);
    }



    public function updateIpMap(){
        $map=new Map();
        $map->updateIpMap();

    }

    public function getIpMapDetail($id){
        $result=Map::get($id);
        return $result;

    }

    public function updateIsOpen(){
        $map = Map::get(input('post.id'));
        $map->is_open=input('post.status');
        $success=$map->save();
//        $success=self::where('id','=',input('post.id'))->update([
//            'is_open'=>input('post.status')
//        ]);
        if(!$success){
            throw new IpMapException([
               'msg'=>'映射状态更新失败',
                'errorCode'=>'30002'
            ]);
        }else{
            throw new SuccessMessage([
                'msg'=>'IP映射状态更新成功'
            ]);
        }
    }


    public function deleteIpMap(){
        $id= request()->header('id');
        $mapdate=Map::get($id);
        $success=Map::destroy($id);




        if(!$success){


            throw new IpMapException([
                'msg'=>'删除失败',
                'errorCode'=>'30003'
            ]);
        }else{
            MapSpite::where('map_id','=',$id)->delete();//删除分散的ip
            (new Log())->log(5,Token::getCurrentUid(),request()->ip(),"删除ip映射{$mapdate['ip_start']}-{$mapdate['ip_end']}");
            throw new SuccessMessage([
                'msg'=>'删除成功',
                //'code'=>204
            ]);
        }
    }

    public function updateIsForbid(){
        $map = Map::get(input('post.id'));
        $map->is_forbid=input('post.status');
        $success=$map->save();
//        $success=self::where('id','=',input('post.id'))->update([
//            'is_open'=>input('post.status')
//        ]);
        if(!$success){
            throw new IpMapException([
                'msg'=>'黑白名单设置失败',
                'errorCode'=>'30002'
            ]);
        }else{
            throw new SuccessMessage([
                'msg'=>'黑白名单设置成功'
            ]);
        }
    }

    public function getIpWhite($pageNumber,$pageSize){
        $map=new Map();
        $pagingOrders = $map->getIpWhite($pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden()
            ->toArray();
        //return json($data);
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);
    }


    public function getIpBlack($pageNumber, $pageSize){
        $map=new Map();
        $pagingOrders = $map->getIpBlack($pageNumber, $pageSize);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden()
            ->toArray();
        //return json($data);
        return json([
            'current_page' => $pagingOrders->currentPage(),
            'rows' => $data,
            'total'=>$pagingOrders->total(),
            'page_size'=>$pageSize
        ]);

    }

}