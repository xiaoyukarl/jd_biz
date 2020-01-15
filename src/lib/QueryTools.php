<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:15
 */

namespace jd\biz\lib;

/**
 * 公共请求
 * Class QueryTools
 * @package jd\biz\lib
 */
class QueryTools
{
    /**
     * 统一请求方法
     * @param $url
     * @param array $params
     * @param array $headers
     * @return array
     */
    public static function query($url, $params = [], $headers = ['Content-Type' => 'application/x-www-form-urlencoded'])
    {
        $curl = new CurlLib();
        $params = http_build_query($params);
        $response = $curl->setHeaders($headers)->post($url, $params);
        if(!$response){
            return HelperLib::returnMsg(4003, "请求接口失败");
        }
        $responseData = json_decode($response, true);
        if($responseData['success'] !== true){
            return HelperLib::returnMsg(4005, $responseData['resultMessage']);
        }
        return HelperLib::returnSuc($responseData['result']);
    }
}