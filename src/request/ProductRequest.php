<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 17:27
 */

namespace jd\biz\request;

use jd\biz\lib\HelperLib;

/**
 * 商品接口
 * Class ProductModel
 * @package jd\biz\model
 */
class ProductRequest extends Request
{

    /**
     * 查询所有商品池编号，商品池编号将用于获取池内商品编号。
     * https://bizapi.jd.com/api/product/getPageNum
     * @return array
     */
    public function getProductPool()
    {
        return $this->queryApi('api/product/getPageNum');
    }

    /**
     * 查询单个商品池下的商品列表。
     * https://bizapi.jd.com/api/product/getSkuByPage
     * @param string $pageNum  商品池编码
     * @param int $pageNo   页码
     * @return array
     */
    public function getSkuByPage($pageNum, $pageNo = 1)
    {
        $postData = [
            'pageNum' => $pageNum,
            'pageNo' => $pageNo
        ];
        return $this->queryApi('api/product/getSkuByPage', $postData);
    }


    /**
     * 查询单个商品的详细信息。
     * https://bizapi.jd.com/api/product/getDetail
     * @param string $sku
     * @param array $queryExts 商品维度扩展字段，当入参输入某个扩展字段后，出参会返回该字段对应的出参。可以根据需要选用。
     * @return array
     */
    public function getDetail($sku, $queryExts = [])
    {
        if(empty($queryExts)){
            $query = [
                'nintroduction','isEnergySaving','isFactoryShip','taxCode','LowestBuy','capacity','spuId','pName',
                'isJDLogistics', 'upc69','contractSkuPoolExt','productFeatures','seoModel','isSelf'
            ];
            $queryExts = implode(',', $query);
        }
        $postData = [
            'sku' => $sku,
            'queryExts' => $queryExts
        ];
        return $this->queryApi('api/product/getDetail', $postData);
    }

    /**
     * 查询单个商品的主图、轮播图。
     * https://bizapi.jd.com/api/product/skuImage
     * @param array $skuIds 商品编号，支持批量，以“,”（半角）分隔  (最高支持100个商品)
     * @return array
     *  其中path是地址，isPrimary为是否是主图，1为主图，0为附图。orderSort为排序图片路径，如上面商品详情页面返回的图片地址一致。
        前缀：需要在前面添加 http://img13.360buyimg.com/n0/  (末尾有/)
        其中 n0(最大图 800*800px)、n1(350*350px)、n2(160*160px)、n3(130*130px)、n4(100*100px) 为图片大小。
        也可以在前面添加 http://img13.360buyimg.com/n0/s450x550_
        其中 s450x550_     是自定义的450*550的图片大小(注意末尾有一个下划线，没有/)
        注意：n0带有京东水印，你用其余的n1-n4不带。s450x450_可以调整450和550为任意大小。利用n0-n4以及s450x450_可以调整图片为任意大小且选择是否要水印；n12大图无水印。
        例如:http://img13.360buyimg.com/n0/s450x550_jfs/t15250/278/846955279/175558/27453f97/5a3b6474N4a944c60.jpg
     */
    public function getImage(array $skuIds)
    {
        if(count($skuIds) > 100){
            return HelperLib::returnMsg(4013, 'skuIds最多支持100种商品');
        }
        $postData = [
            'sku' => implode(',', $skuIds),
        ];
        return $this->queryApi('api/product/skuImage', $postData);
    }

    /**
     * 查询商品的上下架状态，只有上架状态的商品才可售卖。当商品上下架状态变化时，会通过“信息推送API接口”推送信息。
     * https://bizapi.jd.com/api/product/skuState
     * @param array $skuIds 商品编号 (最高支持100个商品)
     * @return array
     */
    public function saleStatus( array $skuIds)
    {
        if(count($skuIds) > 100){
            return HelperLib::returnMsg(4013, 'skuIds最多支持100种商品');
        }
        $postData = [
            'sku' => implode(',', $skuIds),
        ];
        return $this->queryApi('api/product/skuState', $postData);
    }

    /**
     * 查询商品可售性、是否支持专票等影响销售的重要属性。
     * https://bizapi.jd.com/api/product/check
     * @param array $skuIds
     * @return array
     */
    public function checkProduct(array $skuIds)
    {
        if(count($skuIds) > 100){
            return HelperLib::returnMsg(4013, 'skuIds最多支持100种商品');
        }
        $postData = [
            'skuIds' => implode(',', $skuIds),
        ];
        return $this->queryApi('api/product/check', $postData);
    }

    /**
     * 查询商品在特定区域是否可售。
     * https://bizapi.jd.com/api/product/checkAreaLimit
     * @param array $skuIds
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function checkProductAreaLimit(array $skuIds, $province, $city, $county, $town = 0)
    {
        if(count($skuIds) > 100){
            return HelperLib::returnMsg(4013, 'skuIds最多支持100种商品');
        }
        $postData = [
            'skuIds' => implode(',', $skuIds),
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town
        ];
        $response = $this->queryApi('api/product/checkAreaLimit', $postData);
        //isAreaRestrict true 代表区域受限 false 区域不受限
        if($response['code'] === 0){
            $response['data'] = json_decode($response['data'], true);//此接口特殊处理
        }
        return $response;
    }

    /**
     * 根据此接口查询主商品附带的赠品。购买主商品数量大于赠品要求最多购买数量，不加赠品
     * 购买数量小于赠品要求最少购买数量，不加赠品, 下单时间不在促销时间范围内，不加赠品
     * https://bizapi.jd.com/api/product/getSkuGift
     * @param $skuId
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function getSkuGift($skuId, $province, $city, $county, $town = 0)
    {
        $postData = [
            'skuId' => $skuId,
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town
        ];
        return $this->queryApi('api/product/getSkuGift', $postData);
    }

    /**
     * 根据此接口查询可随主商品一并购买的延保等服务商品。
     * https://bizapi.jd.com/api/product/getYanbaoSku
     * @param array $skuIds
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function getYanbaoSku(array $skuIds, $province, $city, $county, $town = 0)
    {
        if(count($skuIds) > 50){
            return HelperLib::returnMsg(4013, 'skuIds最多支持50种商品');
        }
        $postData = [
            'skuIds' => implode(',', $skuIds),
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town
        ];
        return $this->queryApi('api/product/getYanbaoSku', $postData);
    }

    /**
     * 验证商品在指定区域是否可使用货到付款。
     * https://bizapi.jd.com/api/product/getIsCod
     * @param array $skuIds
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function checkCashOnDelivery(array $skuIds, $province, $city, $county, $town = 0)
    {
        if(count($skuIds) > 100){
            return HelperLib::returnMsg(4013, 'skuIds最多支持100种商品');
        }
        $postData = [
            'skuIds' => implode(',', $skuIds),
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town
        ];
        return $this->queryApi('api/product/getIsCod', $postData);
    }

    /**
     * 批量验证商品在指定区域是否可使用货到付款。
     * https://bizapi.jd.com/api/product/getBatchIsCod
     * @param array $skuIds
     * @param $province
     * @param $city
     * @param $county
     * @param int $town
     * @return array
     */
    public function batchCheckCashOnDelivery(array $skuIds, $province, $city, $county, $town = 0)
    {
        if(count($skuIds) > 100){
            return HelperLib::returnMsg(4013, 'skuIds最多支持100种商品');
        }
        $postData = [
            'skuIds' => implode(',', $skuIds),
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'town' => $town
        ];
        return $this->queryApi('api/product/getBatchIsCod', $postData);
    }

    /**
     * 根据搜索条件查询符合要求的商品列表。
     * https://bizapi.jd.com/api/search/search
     * @param array $params
     * @return array
     */
    public function searchProduct($params = [])
    {
        $paramsRequire = [
            'keyword' => '',//搜索关键字，需要编码
            'catId' => '',//分类Id,只支持三级类目Id
            'pageIndex' => '',//当前第几页
            'pageSize' => '',//当前页数量
            'min' => '',//价格区间,最低价
            'max' => '',//价格区间,最高价
            'brands' => '',//品牌搜索 多个品牌以逗号分隔，需要编码
            'cid1' => '',//一级分类id
            'cid2' => '',//二级分类id
            'sortType' => [
                "sale_desc",//销量降序
                "price_asc",//价格升序
                "price_desc",//价格降序
                "winsdate_desc",//上架时间降序
                "sort_totalsales15_desc",//按销量排序_15天销售额
                "sort_days_15_qtty_desc",//按15日销量排序
                "sort_days_30_qtty_desc",//按30日销量排序
                "sort_days_15_gmv_desc",//按15日销售额排序
                "sort_days_30_gmv_desc",//按30日销售额排序
            ],
            'priceCol' => '',//价格汇总 priceCol=”yes”
            'extAttrCol' => '',//扩展属性汇总extAttrCol=”yes”
        ];
        foreach ($paramsRequire as $key=>$val) {
            if(!isset($paramsRequire[$key])){
                return HelperLib::returnMsg(4011, "[$key]参数不合法");
            }
            if(is_array($val) && !empty($params[$key]) &&!in_array($params[$key], $val)){
                $tips = '['.implode(',', $val).']';
                return HelperLib::returnMsg(4011, "[$key]参数值必须是{$tips}里选择");
            }
        }
        return $this->queryApi('api/search/search', $params);
    }

    /**
     * 查询被指定为同一类的商品，如同一款式不同颜色的商品，需要注意符合此条件的商品并不一定被指定为同类商品。
     * https://bizapi.jd.com/api/product/getSimilarSku
     * @param $skuId
     * @return array
     */
    public function getSimilarProduct($skuId)
    {
        return $this->queryApi('api/product/getSimilarSku', ['skuId' => $skuId]);
    }

    /**
     *  根据分类id查询对应分类信息。
     * https://bizapi.jd.com/api/product/getCategory
     * @param int $categoryId  分类id（可通过商品详情接口查询）
     * @return array
     */
    public function getProductCategory($categoryId)
    {
        return $this->queryApi('api/product/getCategory', ['cid' => $categoryId]);
    }
}