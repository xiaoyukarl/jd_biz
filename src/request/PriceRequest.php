<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-14
 * Time: 11:13
 */

namespace jd\biz\request;


/**
 * 商品价格API
 * Class PriceRequest
 * @package jd\biz\request
 */
class PriceRequest extends Request
{

    /**
     * 批量查询商品售卖价。查询在客户商品池中的商品价格。
     * https://bizapi.jd.com/api/price/getSellPrice
     * @param array $skuIds
     * @param string $queryExts  Price //大客户默认价格(根据合同类型查询价格)，该字段必传。 marketPrice //市场价。 containsTax //税率。出参增加tax,taxPrice,nakedPrice 3个字段
     * @return array
     */
    public function getSellPrice(array $skuIds, $queryExts = 'Price,marketPrice,containsTax')
    {
        $params = [
            'sku' => implode(',', $skuIds),
            'queryExts' => $queryExts
        ];
        return $this->queryApi('api/price/getSellPrice', $params);
    }

}