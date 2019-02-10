<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components\Manager;


use App\Components\Utils;
use App\Models\Paper;

class PaperManager
{

    /*
     * 根据id纸条信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $paper = Paper::where('id', $id)->first();
        return $paper;
    }

    /*
     * 根据条件获取信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $papers = new Paper();
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $papers = $papers->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $papers = $papers->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('look_user_id', $con_arr) && !Utils::isObjNull($con_arr['look_user_id'])) {
            $papers = $papers->wherein('look_user_id', $con_arr['look_user_id']);
        }
        $papers = $papers->orderby('id', 'desc');
        if ($is_paginate) {
            $papers = $papers->paginate(Utils::PAGE_SIZE);
        } else {
            $papers = $papers->get();
        }
        return $papers;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->type_str = Utils::MRYH_AD_TYPE_VAL[$info->type];
        if ($info->admin_id) {
            $info->admin = AdminManager::getById($info->admin_id);
        }
        return $info;
    }

    /*
     * 配置信息
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('context', $data)) {
            $info->context = array_get($data, 'context');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        return $info;
    }

}