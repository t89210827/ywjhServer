<?php
/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2018/1/11
 * Time: 9:43
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiResponse;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\Controller;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;
use Leto\MiniProgramAES\WXBizDataCrypt;


class WechatController extends Controller
{

    //相关配置
    const ACCOUNT_CONFIG = "wechat.mini_program.default";     //配置文件位置
    const BUSI_NAME = "ywjh";      //业务名称

    /*
     * 登录接口，根据code换取openid和session等信息
     *
     * By TerryQi
     *
     * 2018-07-04
     */
    public function login(Request $request)
    {
        $data = $request->all();
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $app = app(self::ACCOUNT_CONFIG);
        $code = $data['code'];  //获取小程序code
        $ret = $app->auth->session($code);
        Log::info(__METHOD__ . " " . "code ret:" . json_encode($ret));
        //判断微信端返回信息，如果失败，则告知前端失败
        if (array_key_exists('errcode', $ret)) {
            return ApiResponse::makeResponse(false, $ret, ApiResponse::INNER_ERROR);
        }
        //如果成功获取openid和uniondid，则进行登录处理
        $data = array(
            'openid' => $ret['openid'],
            'session_key' => $ret['session_key'],
        );
        Log::info(__METHOD__ . " " . "data:" . json_encode($data));
        //进行用户登录/注册
        $user = UserManager::login($data);
        Log::info(__METHOD__ . " " . "user:" . json_encode($user));
        $user->attach = $data;
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 解密接口
     *
     * By TerryQi
     *
     * 2018-07-30
     */
    public function decryptData(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required',
            'iv' => 'required',
            'encryptedData' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user_id = $data['user_id'];
        $code = $data['code'];
        $app = app(self::ACCOUNT_CONFIG);
//        $app = Factory::miniProgram(self::ACCOUNT_CONFIG);
        $result = $app->auth->session($code);
        if (array_key_exists('errcode', $result)) {
            return ApiResponse::makeResponse(false, $result, ApiResponse::INNER_ERROR);
        }
        $sessionKey = $result['session_key'];
        $appid = env('MRYH_XCX_APPID');
        $encryptedData = base64_decode($data['encryptedData']);
        $iv = base64_decode($data['iv']);
//        $encryptedData = $data['encryptedData'];
//        $iv = $data['iv'];

        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $result);

        if ($errCode == 0) {
            //更新用户信息
            $result = json_decode($result, true);
            $user_data = UserManager::convertDecryptDatatoUserData($result);    //转为数据库字段名字
            $user = UserManager::getByIdWithToken($data['user_id']);
            $user = UserManager::setInfo($user, $user_data);
            $user = UserManager::setInfo($user, $user_data);
            $user->save();
            return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, null, ApiResponse::INNER_ERROR);
        }
    }

}