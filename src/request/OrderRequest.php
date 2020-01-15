<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:25
 */

namespace jd\biz\request;

use jd\biz\lib\HelperLib;

/**
 * 订单接口
 * 注意:下单接口调用前，建议先调用实时价格查询接口，判断价格是否发生变化，如变化提示客户价格变化，并刷新页面；价格无变化才调用下单接口下单；
 * 外部客户对接vop下单的流程，如下：
 * 1、客户调用下单接口，我们接口会明确返回下单成功或失败，见success字段
 * 2、客户下单使用三方订单号应该与京东订单号一一对应，下单失败，不能修改三方订单号重新下单（因为大客户系统会使用三方订单号进行防重处理）；
 * 3、如果客户下单失败，下单接口"success"会返回false。resultMessage会返回失败原因。客户可根据失败原因调整下单参数后，使用同一三方订单号，重新下单；
 * 4、下单失败有一种特殊情况是“重复下单”，会返回resultCode为"0008"。如果同一三方订单号已经存在有效订单，则视为重复下单，此时下单结果 result会返回该三方订单号对应订单信息，需要客户系统进行金额和商品、收货人等确认，如一致，可视为下单成功。
 * 5、下单成功后，可使用订单查询接口查询订单详细信息。
 * 6、如果客户调用下单接口，出现超时或其他异常，可稍微等待后，使用订单反查接口（https://bizapi.jd.com/api/order/selectJdOrderIdByThirdOrder）确认是否下单成功。
 * 7、支持礼品卡实物卡下单，但是只能下普票订单，不能跟非实物礼品卡混合下单。
 * Class OrderModel
 * @package jd\biz\model
 */
class OrderRequest extends Request
{

    /**
     * 查询准备提交的订单的运费。
     * https://bizapi.jd.com/api/order/getFreight
     * @param array $skuNums
     * @param int $paymentType //1：货到付款 4：预存款 5：公司转账  101：京东金采 102：商城金采(一般不适用，仅限确认开通商城账期的特殊情况使用，请与业务确认后使用) 20为混合支付
     * @param int $province
     * @param int $city
     * @param int $county
     * @param int $town
     * @return array
     */
    public function getOrderFreight($skuNums, $paymentType, $province, $city, $county, $town = 0)
    {
        $paramsResult = $this->checkParams($skuNums, $paymentType, $province, $city, $county, $town);
        if($paramsResult['code'] !== 0){
            return $paramsResult;
        }
        return $this->queryApi('api/order/getFreight', $paramsResult['data']);
    }


    /**
     * 获取京东预约日历。
     * https://bizapi.jd.com/api/order/promiseCalendarNew
     * @param $skuNums
     * @param $paymentType
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function promiseCalendarNew($skuNums, $paymentType, $province, $city, $county, $town = 0)
    {
        $paramsResult = $this->checkParams($skuNums, $paymentType, $province, $city, $county, $town);
        if($paramsResult['code'] !== 0){
            return $paramsResult;
        }
        $response = $this->queryApi('api/order/promiseCalendarNew', $paramsResult['data']);
        if($response['code'] === 0){
            $response['data'] = json_decode($response['data'], true);
        }
        return $response;
    }

    /**
     * 通用的检查参数方法
     * @param $skuNums
     * @param $paymentType
     * @param $province
     * @param $city
     * @param $county
     * @param $town
     * @return array
     */
    protected function checkParams($skuNums, $paymentType, $province, $city, $county, $town)
    {
        foreach ($skuNums as $skuNum) {
            if(!isset($skuNum['skuId']) || !isset($skuNum['num'])){
                return HelperLib::returnMsg(4012, '子数组格式必须为["skuId"=>"*","num"=>"*"]');
            }
            if($skuNum['num'] <= 0){
                return HelperLib::returnMsg(4012, 'num必须大于0');
            }
        }
        if(count($skuNums) > 50){
            return HelperLib::returnMsg(4013, '最多支持50种商品');
        }
        $params = [
            'sku' => json_encode($skuNums, JSON_UNESCAPED_UNICODE),
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town,
            'paymentType' => $paymentType
        ];
        return HelperLib::returnSuc($params);
    }

    /**
     * 提交订单信息，生成京东订单。
     * https://bizapi.jd.com/api/order/submitOrder
     * @param array $order
     * @return array
     */
    public function submitOrder(array $order)
    {
        $arrPara = [
            'thirdOrder' => 'require', 'sku' => 'require', 'name' => 'require', 'province' => 'require,num',
            'city' => 'require,num', 'county' => 'require,num', 'town' => 'require,num', 'address' => 'require',
            'zip' => '', 'phone' => '', 'mobile' => 'require,num', 'email' => 'require,email', 'remark' => '',
            'invoiceState' => 'require,num', 'invoiceType' => 'require,num', 'selectedInvoiceTitle' => 'require,num',
            'companyName' => '', 'invoiceContent' => 'require', 'paymentType' => 'require,num', 'payDetails' => '',
            'isUseBalance' => 'require,num', 'submitState' => 'require,num', 'invoiceName' => 'require',
            'invoicePhone' => 'require',  'invoiceProvice' => '', 'invoiceCity' => '', 'invoiceCounty' => '',
            'invoiceAddress' => '', 'regCompanyName' => 'require', 'regCode' => 'require', 'regAddr' => '',
            'regPhone' => '', 'regBank' => '', 'regBankAccount' => '', 'reservingDate' => '', 'installDate' => '',
            'needInstall' => '', 'promiseDate' => '', 'promiseTimeRange' => '', 'promiseTimeRangeCode' => '',
            'reservedDateStr' => '', 'reservedTimeRange' => '', 'cycleCalendar' => '', 'poNo' => '',
            'validHolidayVocation' => '', 'thdPurchaserAccount' => '', 'thdPurchaserName' => '', 'thdPurchaserPhone' => '',
        ];
        $order = Request::checkPostData($order, $arrPara);
        if(isset($order['code']) && $order['code'] !== 0){
            return $order;
        }
        foreach ($order['sku'] as $skuNum) {
            if(!isset($skuNum['skuId']) || !isset($skuNum['num'])  || !isset($skuNum['price']) || !isset($skuNum['bNeedGift']) ){
                return HelperLib::returnMsg(4012, 'sku数组格式必须为["skuId"=>"*","num"=>"*","price"=>"*","bNeedGift"=>"*",]');
            }
            if($skuNum['num'] <= 0){
                return HelperLib::returnMsg(4012, 'num必须大于0');
            }
        }
        $order['sku'] = json_encode($order['sku'], JSON_UNESCAPED_UNICODE);
        return $this->queryApi('api/order/submitOrder', $order);
    }

    /**
     * 订单反查接口，根据第三方订单号反查京东的订单号。
     * https://bizapi.jd.com/api/order/selectJdOrderIdByThirdOrder
     * @param string $orderNum 第三方订单号
     * @return array
     */
    public function getJDOrder($orderNum)
    {
        return $this->queryApi('api/order/selectJdOrderIdByThirdOrder', ['thirdOrder' => $orderNum]);
    }

    /**
     * 确认预占库存订单接口。
     * https://bizapi.jd.com/api/order/confirmOrder
     * @param string $jdOrderNum 京东的订单单号(下单返回的父订单号)
     * @return array
     */
    public function confirmOrder($jdOrderNum)
    {
        return $this->queryApi('api/order/confirmOrder', ['jdOrderId' => $jdOrderNum]);
    }

    /**
     * 取消未确认订单接口。
     * https://bizapi.jd.com/api/order/cancel
     * @param string $jdOrderNum 京东的订单单号(父订单号)
     * @return array
     */
    public function cancelOrder($jdOrderNum)
    {
        return $this->queryApi('api/order/cancel', ['jdOrderId' => $jdOrderNum]);
    }

    /**
     * 查询京东订单信息接口。
     * https://bizapi.jd.com/api/order/selectJdOrder
     * @param string $jdOrderNum
     * @param array $queryExts 扩展参数。支持多个状态组合查询[英文逗号间隔] orderType,jdOrderState,name,address,mobile,poNo,finishTime,createOrderTime,paymentType,outTime,invoiceType
     * @return array
     */
    public function orderDetail($jdOrderNum, $queryExts = [])
    {
        $params = [
            'jdOrderId' => $jdOrderNum,
            'queryExts' => implode(',', $queryExts)
        ];
        return $this->queryApi('api/order/selectJdOrder', $params);
    }

    /**
     * 查询配送信息。
     * https://bizapi.jd.com/api/order/orderTrack
     * @param $jdOrderNum
     * @param int $waybillCode
     * @return array
     */
    public function orderTrack($jdOrderNum, $waybillCode = 1)
    {
        $params = [
            'jdOrderId' => $jdOrderNum,
            'waybillCode' => $waybillCode
        ];
        return $this->queryApi('api/order/orderTrack', $params);
    }

    /**
     * 确认收货
     * 仅适用于厂商直送订单。厂商直送订单可使用此接口确认收货并将订单置为完成状态。
     * https://bizapi.jd.com/api/order/confirmReceived
     * @param  string $jdOrderNum 京东订单号
     * @return array
     */
    public function confirmReceivedOrder($jdOrderNum)
    {
        return $this->queryApi('api/order/confirmReceived', ['jdOrderId' => $jdOrderNum]);
    }

    /**
     * 更新采购单号
     * 更新订单上的PO单号，可选择用于配送单、发票等票面展示。
     * https://bizapi.jd.com/api/order/saveOrUpdatePoNo
     * @param $jdOrderNum
     * @param $poNo
     * @return array
     */
    public function saveOrUpdatePoNo($jdOrderNum, $poNo)
    {
        $params = [
            'jdOrderId' => $jdOrderNum,
            'poNo' => $poNo
        ];
        return $this->queryApi('api/order/saveOrUpdatePoNo', $params);
    }

    /**
     * 查询所有新建的订单列表。可用于核对订单。
     * https://bizapi.jd.com/api/checkOrder/checkNewOrder
     * @param string $startDate 开始查询日期，格式2018-11-7（不包含当天）
     * @param int $pageNo  页码，默认1
     * @param int $pageSize  大小取值范围[1,100]，默认20
     * @param string $endDate  结束日期，格式2018-11-7。主要用于查询时间段内，跟date配合使用。
     * @param string $jdOrderIdIndex 最小订单号索引游标，为解决大于1W条订单无法查询问题。
     * @return array
     */
    public function orderList($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    {
        return $this->getOrderList('api/checkOrder/checkNewOrder', $startDate, $pageNo, $pageSize, $endDate, $jdOrderIdIndex);
    }


    /**
     * 查询所有妥投的订单列表。可用于核对订单。
     * https://bizapi.jd.com/api/checkOrder/checkDlokOrder
     * @param string $startDate 开始查询日期，格式2018-11-7（不包含当天）
     * @param int $pageNo  页码，默认1
     * @param int $pageSize  大小取值范围[1,100]，默认20
     * @param string $endDate  结束日期，格式2018-11-7。主要用于查询时间段内，跟date配合使用。
     * @param string $jdOrderIdIndex 最小订单号索引游标，为解决大于1W条订单无法查询问题。
     * @return array
     */
    public function checkDeliveredOrder($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    {
        return $this->getOrderList('api/checkOrder/checkDlokOrder', $startDate, $pageNo, $pageSize, $endDate, $jdOrderIdIndex);
    }

    /**
     * 查询所有拒收的订单列表。可用于核对订单。
     * https://bizapi.jd.com/api/checkOrder/checkRefuseOrder
     * @param string $startDate 开始查询日期，格式2018-11-7（不包含当天）
     * @param int $pageNo  页码，默认1
     * @param int $pageSize  大小取值范围[1,100]，默认20
     * @param string $endDate  结束日期，格式2018-11-7。主要用于查询时间段内，跟date配合使用。
     * @param string $jdOrderIdIndex 最小订单号索引游标，为解决大于1W条订单无法查询问题。
     * @return array
     */
    public function checkRefuseOrder($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    {
        return $this->getOrderList('api/checkOrder/checkRefuseOrder', $startDate, $pageNo, $pageSize, $endDate, $jdOrderIdIndex);
    }

    /**
     * 查询所有完成的订单列表。可用于核对订单。
     * https://bizapi.jd.com/api/checkOrder/checkCompleteOrder
     * @param string $startDate 开始查询日期，格式2018-11-7（不包含当天）
     * @param int $pageNo  页码，默认1
     * @param int $pageSize  大小取值范围[1,100]，默认20
     * @param string $endDate  结束日期，格式2018-11-7。主要用于查询时间段内，跟date配合使用。
     * @param string $jdOrderIdIndex 最小订单号索引游标，为解决大于1W条订单无法查询问题。
     * @return array
     */
    public function checkCompleteOrder($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    {
        return $this->getOrderList('api/checkOrder/checkCompleteOrder', $startDate, $pageNo, $pageSize, $endDate, $jdOrderIdIndex);
    }

    //少写几行代码
    protected function getOrderList($url, $startDate, $pageNo, $pageSize, $endDate, $jdOrderIdIndex)
    {
        $params = [
            'date' => $startDate,
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
            'endDate' => $endDate,
            'jdOrderIdIndex' => $jdOrderIdIndex
        ];
        return $this->queryApi($url, $params);
    }

    /**
     * 查询配送预计送达时间。
     * https://bizapi.jd.com/api/order/getPromiseTips
     * @param $skuId
     * @param $num
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function getPromiseTips($skuId, $num, $province, $city, $county, $town = 0)
    {
        $params = [
            'skuId' => $skuId,
            'num' => $num,
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town,
        ];
        return $this->queryApi('api/order/getPromiseTips', $params);
    }

}