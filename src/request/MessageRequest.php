<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-14
 * Time: 17:31
 */

namespace jd\biz\request;

use jd\biz\lib\HelperLib;

/**
 * 信息推送API
 * Class MessageRequest
 * @package jd\biz\request
 */
class MessageRequest extends Request
{

    /**
     * 获取推送信息接口。
     * https://bizapi.jd.com/api/message/get
     * @param array $type 参考文档
     * @return array
     */
    public function getMessage($type)
    {
        return $this->queryApi('api/message/get', ['type' => implode(',', $type)]);
    }

    /**
     * 根据推送id，删除推送信息接口。
     * https://bizapi.jd.com/api/message/del
     * @param array $ids  (getMessage)中获取的id，支持批量删除，英文逗号间隔，最大100个
     * @return array
     */
    public function delMessage( array $ids)
    {
        if(count($ids) > 100){
            return HelperLib::returnMsg(4014, 'ids最大数量为100个');
        }
        return $this->queryApi('api/message/get', ['id' => implode(',', $ids)]);
    }
}