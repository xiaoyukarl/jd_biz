<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-14
 * Time: 15:37
 */

namespace jd\biz\request;

/**
 * 支付API接口
 * Class PayRequest
 * @package jd\biz\request
 */
class BalanceRequest extends Request
{

    /**
     * 查询金采和预存款余额的余额。
     * https://bizapi.jd.com/api/price/getUnionBalance
     * @param string $pin 京东PIN。必须是相同合同下的pin。
     * @param array $type 1：账户余额。2：金采余额。
     * @return array
     */
    public function getUnionBalance($pin, $type = [1, 2])
    {
        $params = [
            'pin' => $pin,
            'type' => implode(',', $type)
        ];
        return $this->queryApi('api/price/getUnionBalance', $params);
    }

    /**
     * 仅支持预存款余额明细查询，不支持金采余额明细查询。
     * https://bizapi.jd.com/api/price/getBalanceDetail
     * @param int $pageNum 分页查询当前页数，默认为1
     * @param int $pageSize 每页记录数，默认为20
     * @param string $orderNum 订单号
     * @param string $startDate 开始日期，格式必须：yyyyMMdd
     * @param string $endDate 截止日期，格式必须：yyyyMMdd
     * @return array
     */
    public function getBalanceDetail($pageNum = 1, $pageSize = 20, $orderNum = '', $startDate = '', $endDate = '')
    {
        $params = [
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'orderNum' => $orderNum,
        ];
        if(!empty($startDate)){
            $params['startDate'] = $startDate;
        }
        if(!empty($endDate)){
            $params['endDate'] = $endDate;
        }
        return $this->queryApi('api/price/getBalanceDetail', $params);
    }

    /**
     * 下单成功支付失败的情况，可以调用此接口重新支付
     * https://bizapi.jd.com/api/order/doPay
     * @param string $jdOrderNum 京东系统订单号
     * @return array
     */
    public function payOrder($jdOrderNum)
    {
        return $this->queryApi('api/order/doPay', ['jdOrderId' => $jdOrderNum]);
    }
}