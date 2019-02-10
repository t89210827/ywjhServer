<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

//公众号  js安全域名校验文件/////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/MP_verify_u8o0o6vDsLXCjpty.txt', function () {
    return response()->download(realpath(base_path('app')) . '/files/MP_verify_u8o0o6vDsLXCjpty.txt', 'MP_verify_u8o0o6vDsLXCjpty.txt');
});

Route::get('/MP_verify_u8o0o6vDsLXCjpty.txt', function () {
    return response()->download(realpath(base_path('app')) . '/files/MP_verify_u8o0o6vDsLXCjpty.txt', 'MP_verify_u8o0o6vDsLXCjpty.txt');
});

//管理后台 start/////////////////////////////////////////////////////////////////////////////////////////////////

//登录
Route::get('/admin/login', 'Admin\LoginController@login');        //登录
Route::post('/admin/login', 'Admin\LoginController@loginPost');   //post登录请求
Route::get('/admin/loginout', 'Admin\LoginController@loginout');  //注销

Route::group(['prefix' => 'admin', 'middleware' => ['BeforeRequest', 'admin.checkLogin']], function () {

    //首页
    Route::get('/', 'Admin\IndexController@index');       //首页
    Route::get('/index', 'Admin\IndexController@index');  //首页

    //管理员管理
    Route::any('/admin/index', 'Admin\AdminController@index');  //管理员管理首页
    Route::get('/admin/setStatus/{id}', 'Admin\AdminController@setStatus');  //设置管理员状态
    Route::get('/admin/edit', 'Admin\AdminController@edit');  //新建或编辑管理员
    Route::post('/admin/edit', 'Admin\AdminController@editPost');  //新建或编辑管理员
    Route::get('/admin/editMySelf', ['as' => 'editMySelf', 'uses' => 'Admin\AdminController@editMySelf']);  //修改个人资料get
    Route::post('/admin/editMySelf', 'Admin\AdminController@editMySelfPost');  //修改个人资料post

    //广告管理
    Route::any('/ad/index', 'Admin\Ywjh\AdController@index');  //广告管理
    Route::get('/ad/edit', 'Admin\Ywjh\AdController@edit');  //广告管理添加、编辑-get
    Route::post('/ad/edit', 'Admin\Ywjh\AdController@editPost');  //广告管理添加、编辑-post
    Route::get('/ad/setStatus/{id}', 'Admin\Ywjh\AdController@setStatus');  //设置广告状态

    //概览页面
    //    Route::get('/overview/index', ['as' => 'overview.index', 'uses' => 'Admin\OverviewController@index']);       //业务概览
    //    Route::get('/overview/activityStatus', ['as' => 'overview.activityStatus', 'uses' => 'Admin\OverviewController@activityStatus']);       //场次状态
    //    Route::get('/overview/income', ['as' => 'overview.income', 'uses' => 'Admin\OverviewController@income']);       //收入状态
    //    Route::get('/overview/newActivity', ['as' => 'overview.newActivity', 'uses' => 'Admin\OverviewController@newActivity']);       //新增场次状态
    //    Route::get('/overview/order', ['as' => 'overview.order', 'uses' => 'Admin\OverviewController@order']);       //订单数
    //    Route::get('/overview/vote', ['as' => 'overview.vote', 'uses' => 'Admin\OverviewController@vote']);       //投票数

    //用户管理
    Route::any('/user/index', 'Admin\UserController@index');  //用户管理
    Route::get('/user/setStatus/{id}', 'Admin\UserController@setStatus');  //设置用户状态
    Route::get('/user/setType/{id}', 'Admin\UserController@setType');  //设置用户类型
    Route::get('/user/info', 'Admin\UserController@info');  //用户详情
    Route::any('/user/edit', 'Admin\UserController@edit');  //编辑用户信息

    /******每日一画管理****************/

    Route::group(['prefix' => 'mryh', 'middleware' => []], function () {

        //业务概览
        Route::any('/mryhOverview/index', 'Admin\Mryh\MryhOverviewController@index');  //业务概览
        Route::any('/mryhOverview/user', 'Admin\Mryh\MryhOverviewController@user');  //用户趋势图
        Route::any('/mryhOverview/join_article', 'Admin\Mryh\MryhOverviewController@join_article');  //参赛和作品趋势图
        Route::any('/mryhOverview/withdraw_failed', 'Admin\Mryh\MryhOverviewController@withdraw_failed');  //提现成功及失败
        Route::any('/mryhOverview/new_refund_joinOrder', 'Admin\Mryh\MryhOverviewController@new_refund_joinOrder');  //参赛押金及退款金额

        //广告管理
        Route::any('/mryhAD/index', 'Admin\Mryh\MryhADController@index');  //广告管理
        Route::get('/mryhAD/edit', 'Admin\Mryh\MryhADController@edit');  //广告管理添加、编辑-get
        Route::post('/mryhAD/edit', 'Admin\Mryh\MryhADController@editPost');  //广告管理添加、编辑-post
        Route::get('/mryhAD/setStatus/{id}', 'Admin\Mryh\MryhADController@setStatus');  //设置广告状态

        //配置管理
        Route::any('/mryhSetting/index', 'Admin\Mryh\MryhSettingController@index');  //配置管理
        Route::get('/mryhSetting/edit', 'Admin\Mryh\MryhSettingController@edit');  //配置管理添加、编辑-get
        Route::post('/mryhSetting/edit', 'Admin\Mryh\MryhSettingController@editPost');  //配置管理添加、编辑-post
        Route::get('/mryhSetting/setStatus/{id}', 'Admin\Mryh\MryhSettingController@setStatus');  //设置配置状态

        //用户管理
        Route::any('/mryhUser/index', 'Admin\Mryh\MryhUserController@index');  //用户列表

        //活动管理
        Route::any('/mryhGame/index', 'Admin\Mryh\MryhGameController@index');  //活动管理
        Route::get('/mryhGame/edit', 'Admin\Mryh\MryhGameController@edit');  //活动管理添加、编辑-get
        Route::post('/mryhGame/edit', 'Admin\Mryh\MryhGameController@editPost');  //活动管理添加、编辑-post
        Route::get('/mryhGame/setStatus/{id}', 'Admin\Mryh\MryhGameController@setStatus');  //设置活动状态
        Route::get('/mryhGame/copy', 'Admin\Mryh\MryhGameController@copy');  //复制活动

        //优惠券管理
        Route::any('/mryhCoupon/index', 'Admin\Mryh\MryhCouponController@index');  //优惠券管理
        Route::get('/mryhCoupon/edit', 'Admin\Mryh\MryhCouponController@edit');  //优惠券管理添加、编辑-get
        Route::post('/mryhCoupon/edit', 'Admin\Mryh\MryhCouponController@editPost');  //优惠券管理添加、编辑-post
        Route::get('/mryhCoupon/setStatus/{id}', 'Admin\Mryh\MryhCouponController@setStatus');  //设置优惠券状态

        //优惠券派发明细
        Route::any('/mryhUserCoupon/index', 'Admin\Mryh\MryhUserCouponController@index');  //订单明细管理

        //订单明细
        Route::any('/mryhJoinOrder/index', 'Admin\Mryh\MryhJoinOrderController@index');  //订单明细管理

        //参赛明细
        Route::any('/mryhJoin/index', 'Admin\Mryh\MryhJoinController@index');  //参赛明细

        //上传作品明细
        Route::any('/mryhJoinArticle/index', 'Admin\Mryh\MryhJoinArticleController@index');  //参赛明细

        //提现明细
        Route::any('/mryhWithdrawCash/index', 'Admin\Mryh\MryhWithdrawCashController@index');  //提现明细
        Route::get('/mryhWithdrawCash/info', 'Admin\Mryh\MryhWithdrawCashController@info');  //提现详情

        //清分明细
        Route::any('/mryhComputePrize/index', 'Admin\Mryh\MryhComputePrizeController@index');

    });
});









