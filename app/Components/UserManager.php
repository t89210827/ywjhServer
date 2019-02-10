<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use App\Models\GuanZhu;
use App\Models\Login;
use App\Models\User;
use App\Components\Utils;
use Illuminate\Support\Facades\Log;


class UserManager
{

    /*
     * 根据id获取用户信息，带token
     *
     * By TerryQi
     *
     * 2017-09-28
     */
    public static function getByIdWithToken($id)
    {
        $user = User::where('id', '=', $id)->first();
        return $user;
    }

    /*
     * 根据id获取用户信息
     *
     * By TerryQi
     *
     * 2017-09-28
     */
    public static function getById($id)
    {
        $user = self::getByIdWithToken($id);
        if ($user) {
            $user->token = null;
        }
        return $user;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     */
    public static function getInfoByLevel($info, $level)
    {
        if ($info->gender) {
            $info->gender_str = Utils::user_gender_val[$info->gender];
        }
        $info->status_str = Utils::USER_TYPE_VAL[$info->type];
        return $info;
    }


    /*
         * 根据条件获取列表
         *
         * By TerryQi
         *
         * 2018-06-06
         */
    public static function getListByCon($con_arr,$is_paginate)
    {
        $infos = new User();
        //相关条件
        if (array_key_exists('nick_name', $con_arr) && !Utils::isObjNull($con_arr['nick_name'])) {
            $infos = $infos->where('nick_name', '=', $con_arr['nick_name']);
        }
        if (array_key_exists('avatar', $con_arr) && !Utils::isObjNull($con_arr['avatar'])) {
            $infos = $infos->where('avatar', '=', $con_arr['avatar']);
        }
        if (array_key_exists('openid', $con_arr) && !Utils::isObjNull($con_arr['openid'])) {
            $infos = $infos->where('openid', '=', $con_arr['openid']);
        }
        if (array_key_exists('token', $con_arr) && !Utils::isObjNull($con_arr['token'])) {
            $infos = $infos->where('token', '=', $con_arr['token']);
        }

        $infos = $infos->orderby('id', 'desc');
        //配置规则
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }

        return $infos;
    }


    /*
     * 根据user_code和token校验合法性，全部插入、更新、删除类操作需要使用中间件
     *
     * By TerryQi
     *
     * 2017-09-14
     *
     * 返回值
     *
     */
    public static function ckeckToken($id, $token)
    {
        //根据id、token获取用户信息
        $count = User::where('id', '=', $id)->where('token', '=', $token)->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
   * 将小程序的消息解密的数据返回至前端
   *
   * By TerryQi
   *
   * 2018-11-22
   */
    public static function convertDecryptDatatoUserData($decrytData)
    {
        $data = array(
            'openid' => $decrytData['openId'],
            'nick_name' => $decrytData['nickName'],
            'gender' => $decrytData['gender'],
            'language' => $decrytData['language'],
            'city' => $decrytData['city'],
            'province' => $decrytData['province'],
            'country' => $decrytData['country'],
            'avatar' => $decrytData['avatarUrl'],
//            'unionid' => $decrytData['unionId']
        );
        return $data;
    }

    /*
     * 配置用户信息，用于更新用户信息和新建用户信息
     *
     * By TerryQi
     *
     * 2017-09-28
     *
     * PS：公众号和小程序输出的字段不一样
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('nick_name', $data)) {
            $info->nick_name = array_get($data, 'nick_name');
        }
        if (array_key_exists('openid', $data)) {
            $info->openid = array_get($data, 'openid');
        }
        if (array_key_exists('avatar', $data)) {
            $info->avatar = array_get($data, 'avatar');
        }
        if (array_key_exists('token', $data)) {
            $info->token = array_get($data, 'token');
        }
        return $info;
    }

    // 生成guid
    /*
     * 生成uuid全部用户相同，uuid即为token
     *
     */
    public static function getGUID()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));

            $uuid = substr($charid, 0, 8)
                . substr($charid, 8, 4)
                . substr($charid, 12, 4)
                . substr($charid, 16, 4)
                . substr($charid, 20, 12);
            return $uuid;
        }
    }


    /*
   * 生成验证码
   *
   * By TerryQi
   */
    public static function sendVertify($phonenum)
    {
        $vertify_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);  //生成4位验证码
        $vertify = new Vertify();
        $vertify->phonenum = $phonenum;
        $vertify->code = $vertify_code;
        $vertify->save();
        /*
         * 预留，需要触发短信端口进行验证码下发
         */
        if ($vertify) {
            SMSManager::sendSMSVerification($phonenum, $vertify_code);
            return true;
        }
        return false;
    }

    /*
     * 校验验证码
     *
     * By TerryQi
     *
     * 2017-11-28
     */
    public static function judgeVertifyCode($phonenum, $vertify_code)
    {
        $vertify = Vertify::where('phonenum', '=', $phonenum)
            ->where('code', '=', $vertify_code)->where('status', '=', '0')->first();
        if ($vertify) {
            //验证码置为失效
            $vertify->status = '1';
            $vertify->save();
            return true;
        } else {
            return false;
        }
    }

    /*
     * 用户登录接口
     *
     * 说明：针对公众号、小程序等，只有登录流程，没有注册流程
     *
     * By TerryQi
     *
     * 2018-07-03
     *
     * account为账户类型，详见数据字典
     *
     * $data为封装的信息，其中包括登录信息和用户基本信息，需要拆开
     *
     */
    public static function login($data)
    {
        $user = self::loginXCX($data);
        return $user;
    }

    /*
     * 小程序的登录和注册流程
     *
     * By TerryQi
     *
     * 2018-07-04
     *
     * $data中应该包含openid、unionid（可选）、session信息
     */
    public static function loginXCX($data)
    {
        $user = null;   //应返回用户信息
        if (array_key_exists('openid', $data)) {
            $con_arr = array(
                'openid' => $data['openid']
            );
            $login = self::getListByCon($con_arr,false)->first();  //根据unionid获取登录信息

            //如果已经有用户信息，则获取用户信息，并进行登录信息补全，返回用户信息
            if ($login) {
                Log::info(__METHOD__ . " " . "condition2 login:" . json_encode($login));
                $user = UserManager::getByIdWithToken($login->id);
                return $user;
            }
        }
        //注册用户
        $user = new User();
        $user = UserManager::setInfo($user, $data);
        $user->token = UserManager::getGUID();
//        dd($user);
        $user->save();
        return $user;
    }

    /*
     * 小程序补全登录信息，主要解决uniond、openid等缺失的问题
     *
     * By TerryQi
     *
     * 2018-07-03
     *
     * $data中应有busi_name、openid、unionid，分别映射login表中的busi_name、ve_value1、ve_value2
     *
     * $user_id为用户id，因此需要注册成功再处理绑定关系
     *
     * return false：失败 true：成功
     *
     */
    public static function setLoginXCX($user_id, $data)
    {
        //user_id如果为空不能往下走
        if (Utils::isObjNull($user_id)) {
            return null;
        }
        //获取基本信息
        $busi_name = $data['busi_name'];    //业务名
        $openid = $data['openid'];      //openid
        $unionid = null;        //unionid
        if (array_key_exists('unionid', $data)) {
            $unionid = $data['unionid'];        //unionid
        }
        //根据openid获取用户信息
        $con_arr = array(
            've_value1' => $openid
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        Log::info(__METHOD__ . " " . "login:" . json_encode($login));
        //如果有值，就进行信息补全
        if (!$login) {
            $login = new Login();
        }
        $login->user_id = $user_id;
        $login->account_type = Utils::ACCOUNT_TYPE_XCX;
        $login->busi_name = $busi_name;
        $login->ve_value1 = $openid;
        $login->ve_value2 = $unionid;
        $login->save();

        Log::info(__METHOD__ . " " . "login:" . json_encode($login));

        return $login;
    }


    /*
     * 将服务号web.auth的用户数据转为$data形式
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public static function convertFWHDataToData($original_user)
    {
        $data = array(
            "openid" => $original_user['openid'],
            'nick_name' => $original_user['nickname'],
            'gender' => $original_user['sex'],
            'language' => $original_user['language'],
            'avatar' => $original_user['headimgurl'],
            'country' => $original_user['country'],
            'province' => $original_user['province'],
            'city' => $original_user['city'],
            'busi_name' => $original_user['busi_name']
        );
        if (array_key_exists('unionid', $original_user)) {
            $data['unionid'] = $original_user['unionid'];
        }
        return $data;
    }


    /*
     * 将服务号的session_val转换为user_data数组数据
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public static function convertSessionValToUserData($session_val, $busi_name)
    {
        //获取用户相关信息
//        Log::info(__METHOD__ . " session_val : " . $session_val);
        $user_val = $session_val['default']->toArray();


        Log::info("user_val:" . json_encode($user_val));
        $original_user = $user_val['original']; //获取用户基本信息
        $original_user['busi_name'] = $busi_name;
        //封装数据
        $user_data = self::convertFWHDataToData($original_user);
        return $user_data;
    }


    /*
     * 业务统计数据
     *
     * By TerryQi
     *
     * 2018-08-14
     */
    public static function addStatistics($user_id, $item, $num)
    {
        $user = self::getByIdWithToken($user_id);
        switch ($item) {
            case "yq_num":
                $user->yq_num = $user->yq_num + $num;
                break;
            case "rel_num":
                $user->rel_num = $user->rel_num + $num;
                break;
        }
        $user->save();
    }

    /*
 * 进行消息解密
 *
 * By TerryQi
 *
 * 2018-11-22
 *
 * @app为外部信息，code、vi（注意已经解密）、encryptedData（注意已经解密）、env_appid_name
 */
    public static function decryptData($app, $code, $iv, $encryptedData, $env_appid_name)
    {
        $code = $code;
        $result = $app->auth->session($code);
        //如果出错，返回null
        if (array_key_exists('errcode', $result)) {
            return null;
        }
        $sessionKey = $result['session_key'];
        $appid = env($env_appid_name);
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $result);
        Utils::processLog(__METHOD__, '', "errorCode:" . json_encode($errCode));
        if ($errCode == 0) {
            return (array)json_decode($result, true);
        } else {
            return null;
        }
    }

}