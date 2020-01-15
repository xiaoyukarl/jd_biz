<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:40
 */

namespace jd\biz\request;


use jd\biz\lib\ConfigLib;
use jd\biz\lib\HelperLib;
use jd\biz\lib\QueryTools;
use jd\biz\lib\TokenLib;

class Request
{

    /**
     * 获取url
     * @param $uri
     * @return string
     */
    protected function getFullUrl($uri)
    {
        return ConfigLib::get('jdBaseUrl') . $uri;
    }

    /**
     * 公共方法
     * @param $url
     * @param array $postData
     * @return array
     */
    protected function queryApi($url, $postData = [])
    {
        $fullUrl = $this->getFullUrl($url);
        $tokenLib = new TokenLib();
        $result = $tokenLib->getAccessToken();
        if($result['code'] === 0){
            $postData['token'] = $result['data']['accessToken'];
            return QueryTools::query($fullUrl, $postData);
        }
        return $result;
    }

    /**
     * 根据参数验证
     * @param array $postData
     * @param array $arrPara
     * @return array
     */
    public static function checkPostData($postData, $arrPara)
    {
        $arrRtn = array();
        foreach($arrPara as $k=>$v) {
            $postVal = isset($postData[$k]) ? (is_array($postData[$k]) ? $postData[$k] : trim($postData[$k])) : '';
            $arrRtn[$k] = $postVal; //赋值

            //校验开始 start
            $arrCheckInfo = explode(',', $v);
            foreach($arrCheckInfo as $val) {
                $errorMsg = '';
                switch($val) {
                    case 'require':
                        if($postVal=='') {
                            $errorMsg = ("{$k} is empty");
                        }
                        break;
                    case 'num':
                        if( isset($postData[$k]) && $postData[$k]!='' && (!is_numeric($postData[$k])) ) {
                            $errorMsg = ("{$k} is not a number");
                        }
                        break;
                    case 'boolean':
                        if( isset($postData[$k]) && !is_bool($postData[$k]) ) {
                            $errorMsg = ("{$k} is not a boolean");
                        }
                        break;
                    case 'array':
                        if( isset($postData[$k]) && !is_array($postData[$k]) ) {
                            $errorMsg = ("{$k} is not a array");
                        }
                        break;
                    case 'email':
                        if(!empty($postData[$k])) {
                            if(!preg_match('/^[a-zA-Z0-9_\-\.]{2,30}@\w+(\.\w{2,4}){1,3}$/i', $postData[$k])) {
                                $errorMsg = ("{$k} format error");
                            }
                        }
                        break;
                }
                if (!empty($errorMsg)) {
                    return HelperLib::returnMsg(4004,$errorMsg);
                }
            }
        }
        return $arrRtn;
    }
}