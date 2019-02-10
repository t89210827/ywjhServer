<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['cors']], function () {
    Route::get('test', function () {
        return 'Hello World';
    });

    //获取openid
    Route::post('wechat/login', 'API\WeChatController@login');        //登录接口
    Route::post('wechat/decryptData', 'API\WeChatController@decryptData')->middleware('user.checkToken');        //消息解密
    Route::post('user/updateById', 'API\UserController@updateById')->middleware('user.checkToken'); //更新用户信息

    //广告轮播图
    Route::get('ad/getById', 'API\ADController@getById');
    Route::get('ad/getListByCon', 'API\ADController@getListByCon');

    //小纸条
    Route::post('paper/addPaper', 'API\paperController@addPaper')->middleware('user.checkToken');
    Route::get('paper/getPaperById', 'API\paperController@getPaperById');
    Route::get('paper/getListByCon', 'API\paperController@getListByCon');

});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
////    return $request->user();
//    return $request;
//});

//Route::get('/user', 'UsersController@index');