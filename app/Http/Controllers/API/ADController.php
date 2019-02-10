<?php
/**
 * Created by PhpStorm.
 * User: Acker
 * Date: 2018/11/12
 * Time: 1:27
 */
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Components\Manager\ADManager;
use App\Http\Controllers\ApiResponse;


class ADController{
    /*
     *
     * 获取轮播图
     *
     */
    public function getListByCon(Request $request){
        $data = $request->all();
        $con_arr = array(
            'status' => '1',
        );
        $ads = ADManager::getListByCon($con_arr, false);
        return ApiResponse::makeResponse(true, $ads, ApiResponse::SUCCESS_CODE);
    }
}