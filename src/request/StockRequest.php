<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-14
 * Time: 11:21
 */

namespace jd\biz\request;


use jd\biz\lib\HelperLib;

/**
 * 库存API
 * Class StockRequest
 * @package jd\biz\request
 */
class StockRequest extends Request
{

    /**
     * 批量获取库存接口。批量查询在客户指定区域的库存信息，最多返回数量50，超过100统一返回有货。
     * 实际库存为50--100，但用户查询数量大于真实库存数量时显示“无货”，小于等于真实库存数量时显示“有货”。
     * https://bizapi.jd.com/ api/stock/getNewStockById
     * @param array $skuNums
     * @param array $area
     * @return array
     */
    public function getNewStockById(array $skuNums, array $area)
    {
        foreach ($skuNums as $skuNum) {
            if(!isset($skuNum['skuId']) || !isset($skuNum['num'])){
                return HelperLib::returnMsg(4012, '子数组格式必须为["skuId"=>"*","num"=>"*"]');
            }
        }

        if(count($skuNums) > 100){
            return HelperLib::returnMsg(4013, '最多支持100种商品');
        }
        $params = [
            'skuNums' => json_encode($skuNums, JSON_UNESCAPED_UNICODE),
            'area' => implode('_', $area),
        ];
        $response = $this->queryApi('api/stock/getNewStockById', $params);
        $response['data'] = json_decode($response['data'], true);
        foreach ($response['data'] as &$pro){
            $pro['areaId'] = explode('_', $pro['areaId']);
        }
        return $response;
    }
}