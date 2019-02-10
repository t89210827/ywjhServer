<?php

namespace App\Http\Controllers\API;

use App\Components\RequestValidator;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Paper;
use App\Models\test;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\Manager\PaperManager;

class paperController extends Controller
{
    /*
     *
     */
    public function getPaperById(Request $request)
    {
        $data = $request->all();

        $requestValidationResult = RequestValidator::validator($request->all(), [
            'paper_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "纸条id未传入", ApiResponse::MISSING_PARAM);
        }
        $paper = PaperManager::getById($data['paper_id']);
        $user = UserManager::getById($paper["user_id"]);
        $paper["user"] = $user;
        if ($paper) {
            return ApiResponse::makeResponse(true, $paper, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, "未找到该纸条", ApiResponse::NO_USER);
        }
    }


    /*
     *
     * 查看纸条列表
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        $paper = PaperManager::getListByCon($data, false);
        return ApiResponse::makeResponse(true, $paper, ApiResponse::SUCCESS_CODE);
    }

    /*
     *
     * 添加纸条
     */
    public function addPaper(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'context' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "您的纸条不能为空", ApiResponse::MISSING_PARAM);
        }
        $data["status"] = 1;
        $paper = new Paper();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $paper = PaperManager::getById($data['id']);
        }
        $paper = PaperManager::setInfo($paper, $data);
        $paper->save();
        return ApiResponse::makeResponse(true, $paper, ApiResponse::SUCCESS_CODE);
    }
}
