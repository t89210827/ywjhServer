<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Ywjh;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\Manager\ADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\AD;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AdController
{
    //首页
    public function index(Request $request)
    {
//      $data = $request->all();
        $con_arr = array();
        $ads = ADManager::getListByCon($con_arr, true);
        return view('admin.ywjh.ad.index', ['datas' => $ads, 'con_arr' => $con_arr]);
    }

    //设置活动广告状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数活动广告id$id']);
        }
        $ad = ADManager::getById($id);
        $ad->status = $data['status'];
        $ad->save();
        return ApiResponse::makeResponse(true, $ad, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 添加、编辑投票活动广告-get
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $ad = new AD();
        if (array_key_exists('id', $data)) {
            $ad = ADManager::getById($data['id']);
        }
        return view('admin.ywjh.ad.edit', ['admin' => $admin, 'data' => $ad, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑投票活动广告-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $ad = new AD();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $ad = AdManager::getById($data['id']);
        }
        $ad = AdManager::setInfo($ad, $data);
//        $ad->admin_id = $admin->id;      //记录活动广告id
        $ad->save();
        return ApiResponse::makeResponse(true, $ad, ApiResponse::SUCCESS_CODE);
    }

}