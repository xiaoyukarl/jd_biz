<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:27
 */

namespace jd\biz\request;

/**
 * 发票接口
 * Class InvoiceModel
 * @package jd\biz\model
 */
class InvoiceRequest extends Request
{

    /**
     * 申请开票
     * https://bizapi.jd.com/api/invoice/submit
     * @param array $invoice
     * @return array
     */
    public function createInvoice(array $invoice)
    {
        $arrPara = [
            'supplierOrder' => 'require', 'markId' => 'require', 'settlementId' => 'require',
            'settlementNum' => '', 'settlementNakedPrice' => '', 'settlementTaxPrice' => '', 'invoiceType' => 'require,num',
            'invoiceOrg' => 'require,num', 'bizInvoiceContent' => 'require,num', 'invoiceDate' => 'require',
            'title' => 'require', 'billToParty' => '', 'enterpriseTaxpayer' => '', 'billToer' => '', 'billToContact' => 'require',
            'billToProvince' => '', 'billToCity' => '', 'billToCounty' => '', 'billToTown' => '',
            'billToAddress' => '', 'repaymentDate' => '', 'invoiceNum' => 'require,num', 'invoiceNakedPrice' => '',
            'invoiceTaxPrice' => '', 'invoicePrice' => 'require', '' => '', 'currentBatch' => 'require', 'totalBatch' => 'require,num',
            'totalBatchInvoiceNakedAmount' => '', 'totalBatchInvoiceTaxAmount' => '', 'totalBatchInvoiceAmount' => 'require,num',
            'billingType' => '', 'isMerge' => '', 'poNo' => '', 'enterpriseRegAddress' => '', 'enterpriseRegPhone' => '',
            'enterpriseBankName' => '', 'enterpriseBankAccount' => '',
        ];
        $result = Request::checkPostData($invoice, $arrPara);
        if(isset($result['code']) && $result['code'] !== 0){
            return $result;
        }
        return $this->queryApi('api/invoice/submit', $result['data']);
    }

    /**
     * 通过的订单号查询对应的第三方申请单号。
     * https://bizapi.jd.com/api/invoice/queryThrApplyNo
     * @param $jdOrderNum
     * @return array
     */
    public function queryThrApplyNo($jdOrderNum)
    {
        return $this->queryApi('api/invoice/queryThrApplyNo', ['jdOrderId' => $jdOrderNum]);
    }


    /**
     * 查询第三方申请单号下的发票概要信息。
     * https://bizapi.jd.com/api/invoice/select
     * @param string $markId 第三方申请单号：申请发票的唯一id标识
     * @return array
     */
    public function queryInvoiceDesc($markId)
    {
        return $this->queryApi('api/invoice/select', ['markId' => $markId]);
    }

    /**
     * 查询发票明细信息。目前只支持纸质发票。
     * https://bizapi.jd.com/api/invoice/queryInvoiceItem
     * @param string $invoiceId 发票号
     * @param string $invoiceCode 发票代码
     * @return array
     */
    public function queryInvoiceItem($invoiceId, $invoiceCode)
    {
        return $this->queryApi('api/invoice/queryInvoiceItem', [
            'invoiceId' => $invoiceId,
            'invoiceCode' => $invoiceCode,
        ]);
    }

    /**
     * 查询电子发票明细信息。
     * https://bizapi.jd.com/api/invoice/getInvoiceList
     * @param string $jdOrderNum 京东订单号
     * @param int $invoiceType 发票类型：1 普票，2 专票，3 电子发票
     * @param array $queryExts prefixZero：专票发票号前面补齐零 electronicVAT：专票电子化，（返回独立的对象：BizIvcChainInvoiceRespVo）
     * @return array
     */
    public function getInvoiceList($jdOrderNum, $invoiceType, $queryExts = ['prefixZero', 'electronicVAT'])
    {
        $params = [
            'jdOrderId' => $jdOrderNum,
            'ivcType' => $invoiceType,
            'queryExts' => implode(',', $queryExts)
        ];
        return $this->queryApi('api/invoice/getInvoiceList', $params);
    }

    /**
     * 纸质发票如果需要邮寄，使用此接口查询配送单号。
     * https://bizapi.jd.com/api/invoice/waybill
     * @param $markId
     * @return array
     */
    public function getInvoiceWaybill($markId)
    {
        return $this->queryApi('api/invoice/waybill', ['markId' => $markId]);
    }

    /**
     * 查询发票物流消息信息。
     * https://bizapi.jd.com/api/invoice/queryDeliveryNo
     * @param $jdOrderNum
     * @return array
     */
    public function queryDeliveryNo($jdOrderNum)
    {
        return $this->queryApi('api/invoice/queryDeliveryNo', ['jdOrderId' => $jdOrderNum]);
    }

    /**
     * 取消已经提交的开票申请。
     * https://bizapi.jd.com/api/invoice/cancel
     * @param $markId
     * @return array
     */
    public function cancelInvoice($markId)
    {
        return $this->queryApi('api/invoice/cancel', ['markId' => $markId]);
    }

}