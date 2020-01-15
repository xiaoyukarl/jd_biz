<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 15:35
 */

namespace jd\biz\lib;


class HelperLib
{
    /**
     * 返回成功格式数组
     * @param array $data
     * @param string $message
     * @return array
     */
    public static function returnSuc($data = [], $message = 'success')
    {
        return [
            'code' => 0,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * 返回失败格式数组
     * @param $code
     * @param string $message
     * @param array $data
     * @return array
     */
    public static function returnMsg($code, $message = '', $data = [])
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}