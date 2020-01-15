<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:26
 */

namespace jd\biz\request;

/**
 * 地址接口
 * Class AddressModel
 * @package jd\biz\model
 */
class AddressRequest extends Request
{

    /**
     * 获取省份
     * api:https://bizapi.jd.com/api/area/getProvince
     * @return array
     */
    public function getProvinces()
    {
        return $this->queryApi('api/area/getProvince');
    }

    /**
     * 获取城市
     * https://bizapi.jd.com/api/area/getCity
     * @param $provinceId
     * @return array
     */
    public function getCity($provinceId)
    {
        return $this->queryApi('api/area/getCity', ['id' => $provinceId]);
    }

    /**
     * 获取区县
     * https://bizapi.jd.com/api/area/getCounty
     * @param $cityId
     * @return array
     */
    public function getCounty($cityId)
    {
        return $this->queryApi('api/area/getCounty', ['id' => $cityId]);
    }

    /**
     * 获取镇级地址
     * https://bizapi.jd.com/api/area/getTown
     * @param $countyId
     * @return array
     */
    public function getTown($countyId)
    {
        return $this->queryApi('api/area/getTown',  ['id' => $countyId]);
    }

    /**
     * 验证地址有效性
     * 验证已编码为京东一至四级地址ID的有效性。
     * https://bizapi.jd.com/api/area/checkArea
     * @param array $address
     * @return array
     */
    public function checkArea(array $address)
    {
        return $this->queryApi('api/area/checkArea', $address);
    }

    /**
     * 地址详情转换京东地址编码
     * https://bizapi.jd.com/api/area/getJDAddressFromAddress
     * @param string $address
     * @return array
     */
    public function getJDAddressFromAddress($address)
    {
        return $this->queryApi('api/area/getJDAddressFromAddress', ['address' => $address]);
    }

}