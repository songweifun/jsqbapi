<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];

use think\Route;
//Route::rule('api/v1/banner/:id','api/v1.Banner/getBanner');
//Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');
//
//Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
//Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');
//
//Route::get('api/:version/product/by_category','api/:version.Product/getAllIncategory');
//Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//Route::get('api/:version/product/recent','api/:version.Product/getRecent');

//Route::group('api/:version/product',function (){
//    Route::get('/by_category','api/:version.Product/getAllIncategory');
//    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//    Route::get('/recent','api/:version.Product/getRecent');
//
//});


//Route::get('api/:version/category/all','api/:version.Category/getAllcategories');


//token
//Route::post('api/:version/token/code','api/:version.Token/getUserCode');
Route::post('api/:version/token/user','api/:version.Token/getToken');
Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');


//address
//Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
//Route::get('api/:version/address', 'api/:version.Address/getUserAddress');


//order
Route::post('api/:version/order','api/:version.Order/placeOrder');

Route::any('api/:version/order/new','api/:version.Order/getSummaryByNew');
Route::post('api/:version/order/send','api/:version.Order/sendOrderHandler');


Route::post('api/:version/order/update_orderurl','api/:version.Order/asyncUpadateUrl');


Route::get('api/:version/order/user_order','api/:version.Order/getSendOrderByUserNew');
Route::get('api/:version/order/user_order_all','api/:version.Order/getSendOrderByUserAll');




//Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',[], ['id'=>'\d+']);
//Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');
//
//
////Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');
//
////不想把所有查询都写在一起，所以增加by_user，很好的REST与RESTFul的区别
Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');
//
//
//Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');


//Upload
Route::post('api/:version/upload','api/:version.Upload/fileUpload');


//system
Route::post('api/:version/system/map','api/:version.System/ipMap');
Route::get('api/:version/system/all_map','api/:version.System/getAllMap');
Route::post('api/:version/system/update_map','api/:version.System/updateIpMap');
Route::get('api/:version/system/one_map','api/:version.System/getIpMapDetail');
Route::post('api/:version/system/update_map_status','api/:version.System/updateIsOpen');
Route::delete('api/:version/system/delete_map','api/:version.System/deleteIpMap');
Route::post('api/:version/system/update_map_forbid','api/:version.System/updateIsForbid');
Route::get('api/:version/system/ip_white','api/:version.System/getIpWhite');
Route::get('api/:version/system/ip_black','api/:version.System/getIpBlack');
Route::get('api/:version/system/library','api/:version.System/getLibrarys');
Route::post('api/:version/system/update_library_open','api/:version.System/updateLibraryOpen');
Route::post('api/:version/system/add_library','api/:version.System/addLibrary');
Route::post('api/:version/system/update_library','api/:version.System/updateLibrary');
Route::get('api/:version/system/one_library','api/:version.System/getLibraryDetail');
Route::delete('api/:version/system/delete_library','api/:version.System/deleteLibrary');

Route::get('api/:version/system/schools','api/:version.System/getSchools');


Route::get('api/:version/statistics/order_byschool','api/:version.Statistics/getOrderStatisticsBySchool');
Route::get('api/:version/statistics/resource_byschool','api/:version.Statistics/getResourceStatisticsBySchool');
Route::get('api/:version/statistics/requestip_top10','api/:version.Statistics/getRequstStatisticsByIpTop10');
Route::get('api/:version/statistics/requestmap_top10','api/:version.Statistics/getRequestStatisticsByMapNameTop10');







