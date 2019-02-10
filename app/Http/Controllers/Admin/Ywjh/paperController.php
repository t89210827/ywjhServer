<?php

namespace App\Http\Controllers\Admin\Ywjh;

use App\Components\RequestValidator;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Paper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\Manager\PaperManager;

class paperController extends Controller
{
    /*
     *
     * 添加纸条
     */
    public function addPaper(Request $request)
    {
        dd(111);
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'context' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        if (array_key_exists('statue', $data) && !Utils::isObjNull($data['statue'])) {
            $data->statue = 1;
        }
        $paper = new Paper();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $ad = PaperManager::getById($data['id']);
        }
        $paper = PaperManager::setInfo($paper, $data);
        $paper->save();
        return ApiResponse::makeResponse(true, $paper, ApiResponse::SUCCESS_CODE);
    }
}
