<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:28
 */

namespace jd\biz\request;

/**
 * 售后接口
 * Class AfterSaleModel
 * @package jd\biz\model
 */
class AfterSaleRequest extends Request
{

    /**
     * 校验某订单中某商品是否可以提交售后服务
     * https://bizapi.jd.com/api/afterSale/getAvailableNumberComp
     * @param string $jdOrderNum 京东订单号
     * @param int $skuId  京东商品编号
     * @return array
     */
    public function getAvailableNumberComp($jdOrderNum, $skuId)
    {
        return $this->commonQuery('api/afterSale/getAvailableNumberComp', [
            'jdOrderId' => $jdOrderNum,
            'skuId' => $skuId
        ]);
    }

    /**
     * 根据订单号、商品编号查询支持的服务类型
     * https://bizapi.jd.com/api/afterSale/getCustomerExpectComp
     * @param $jdOrderNum
     * @param $skuId
     * @return array
     */
    public function getCustomerExpectComp($jdOrderNum, $skuId)
    {
        return $this->commonQuery('api/afterSale/getCustomerExpectComp', [
            'jdOrderId' => $jdOrderNum,
            'skuId' => $skuId
        ]);
    }

    /**
     * 根据订单号、商品编号查询支持的商品返回京东方式
     * https://bizapi.jd.com/api/afterSale/getWareReturnJdComp
     * @param $jdOrderNum
     * @param $skuId
     * @return array
     */
    public function getWareReturnJdComp($jdOrderNum, $skuId)
    {
        return $this->commonQuery('api/afterSale/getWareReturnJdComp', [
            'jdOrderId' => $jdOrderNum,
            'skuId' => $skuId
        ]);
    }

    /**
     * 发起售后申请 。
     * 需要该配送单已经妥投。
     * 需要先调用(getAvailableNumberComp)接口校验订单中某商品是否可以提交售后服务
     * 需要先调用(getCustomerExpectComp)查询支持的服务类型
     * 需要先调用(getWareReturnJdComp)接口查询支持的商品返回京东方式
     * https://bizapi.jd.com/api/afterSale/createAfsApply
     * @param string $params 售后参数
     * @return array
     */
    public function createAfsApply($params)
    {
        $arrPara = [
            'jdOrderId' => 'require', 'customerExpect' => 'require,num', 'questionDesc' => '',
            'isNeedDetectionReport' => 'require,boolean', 'questionPic' => '', 'isHasPackage' => 'require,boolean',
            'packageDesc' => 'require,num', 'asCustomerDto' => 'require,array', 'asPickwareDto '=> 'require,array',
            'asReturnwareDto' => 'require,array', 'asDetailDto' => 'require,array',
        ];
        $paramsResult = Request::checkPostData($params, $arrPara);
        if(isset($paramsResult['code']) && $paramsResult['code'] !== 0){
            return $paramsResult;
        }
        return $this->commonQuery('api/afterSale/createAfsApply', $paramsResult['data']);
    }

    /**
     * 填写发运信息
     * 如果商品需要逆向发往京东，当选择第三方配送时，使用此接口填写配送信息。
     * 接口依赖：
     * 需要调用9.6 查询得到服务单号
     * 并且有需要补充客户发运信息时调用这个接口
     * https://bizapi.jd.com/api/afterSale/updateSendSku
     * @param string $afsServiceId
     * @param string $expressCompany 发运公司：圆通快递、申通快递、韵达快递、中通快递、宅急送、EMS、顺丰快递
     * @param string $deliverDate 发货日期，格式为yyyy-MM-dd HH:mm:ss
     * @param string $expressCode 货运单号，最大50字符
     * @param float $freightMoney 运费
     * @return array
     */
    public function updateSendSku($afsServiceId, $expressCompany, $deliverDate, $expressCode, $freightMoney = 0.0)
    {
        $params = [
            'afsServiceId' => $afsServiceId,
            'expressCompany' => $expressCompany,
            'deliverDate' => $deliverDate,
            'expressCode' => $expressCode,
        ];
        if($params['freightMoney'] > 0){
            $params['freightMoney'] = $freightMoney;
        }
        return $this->commonQuery('api/afterSale/updateSendSku', $params);
    }

    /**
     * 查询订单下服务单汇总信息。
     * https://bizapi.jd.com/api/afterSale/getServiceListPage
     * @param string $jdOrderNum 京东订单号
     * @param int $pageIndex 页码，1代表第一页
     * @param int $pageSize 每页记录数, 大小取值范围[1,100]
     * @return array
     */
    public function getServiceListPage($jdOrderNum, $pageIndex = 1, $pageSize = 20)
    {
        $params = [
            'jdOrderId' => $jdOrderNum,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
        ];
        return $this->commonQuery('api/afterSale/getServiceListPage', $params);
    }

    /**
     * 查询服务单明细信息。
     * https://bizapi.jd.com/api/afterSale/getServiceDetailInfo
     * @param $afsServiceId
     * @param array $appendInfoSteps
     * @return array
     */
    public function getServiceDetailInfo($afsServiceId, $appendInfoSteps = [1,2,3,4,5])
    {
        $params = [
            'afsServiceId' => $afsServiceId,
            'appendInfoSteps' => $appendInfoSteps,
        ];
        return $this->commonQuery('api/afterSale/getServiceDetailInfo', $params);
    }


    /**
     * 取消已经生成的服务单。
     * https://bizapi.jd.com/api/afterSale/auditCancel
     * @param array $serviceIdList 京东售后服务单集合
     * @param string $approveNotes 审核意见
     * @return array
     */
    public function auditCancel(array $serviceIdList, $approveNotes)
    {
        $params = [
            'serviceIdList' => $serviceIdList,
            'approveNotes' => $approveNotes,
        ];
        return $this->commonQuery('api/afterSale/auditCancel', $params);
    }

    /**
     * 确认服务单
     * https://bizapi.jd.com/api/afterSale/confirmAfsOrder
     * @param $customerName
     * @param $username
     * @param $afsServiceId
     * @return array
     */
    public function confirmAfsOrder($customerName, $username, $afsServiceId)
    {
        $params = [
            'customerName' => $customerName,
            'username' => $username,
            'afsServiceId' => $afsServiceId,
        ];
        return $this->commonQuery('api/afterSale/auditCancel', $params);
    }

    /**
     * 查询订单下服务单汇总列表信息。
     * https://bizapi.jd.com/api/afterSale/getAfsServiceListPage
     * @param array $query
     * @return array
     */
    public function getAfsServiceListPage(array $query = ['pageIndex' => 1, 'pageSize' => 20])
    {
        $arrPara = [
            'username' => '', 'pageIndex' => 'require,num', 'pageSize' => 'require,num',
            'startDate' => 'require', 'endDate' => 'require', 'jdOrderId' => '', 'sku' => ''
        ];
        $result = Request::checkPostData($query, $arrPara);
        if(isset($result['code']) && $result['code'] !== 0){
            return $result;
        }
        return $this->commonQuery('api/afterSale/getAfsServiceListPage', $result['data']);
    }

    /**
     * 少写几行重复代码
     * @param $url
     * @param array $param
     * @return array
     */
    protected function commonQuery($url, $param)
    {
        $params['param'] = json_encode($param, JSON_UNESCAPED_UNICODE);
        return $this->queryApi($url, $params);
    }



}