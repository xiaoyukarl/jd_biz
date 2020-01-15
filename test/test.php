<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 15:46
 */

require_once '../vendor/autoload.php';

$tokenLib = new \jd\biz\lib\TokenLib();
//var_dump($tokenLib->getAccessToken());exit;
$address = new \jd\biz\request\AddressRequest();
$product = new \jd\biz\request\ProductRequest();
$price = new \jd\biz\request\PriceRequest();
$stock = new \jd\biz\request\StockRequest();
$order = new \jd\biz\request\OrderRequest();
$pay =  new \jd\biz\request\BalanceRequest();
$afterSale = new \jd\biz\request\AfterSaleRequest();
$message = new \jd\biz\request\MessageRequest();



var_dump($address->getProvinces());exit;
//var_dump($address->getCity(19));exit;
//var_dump($address->getCounty(1634));exit;
//var_dump($address->getTown(1640));exit;//8495
//var_dump($address->checkArea([
//    'provinceId' => '19',
//    'cityId' => '1634',
//    'countyId' => '1640',
//    'townId' => '8495',
//]));exit;

//var_dump($address->getJDAddressFromAddress('广东省梅州市五华县 龙村镇 大和村'));exit;
//var_dump($product->getProductPool());exit;
//var_dump($product->getSkuByPage('32175701'));exit;
//var_dump($product->getDetail('305821'));exit;
//var_dump($product->getImage(['101609']));exit;
//var_dump($product->saleStatus(['101609']));exit;
//var_dump($product->checkProduct(['305821']));exit;
//var_dump($product->checkProductAreaLimit(['101609'], 19,1634, 1640));exit;
//var_dump($product->getSkuGift('305821', 19,1634, 1640));exit;
//var_dump($product->getYanbaoSku(['305821'], 19,1634, 1640));exit;
//var_dump($product->checkCashOnDelivery(['305821'], 19,1634, 1640));exit;
//var_dump($product->batchCheckCashOnDelivery(['305821', '101609'], 19,1634, 1640));exit;
//var_dump($product->searchProduct(['keyword'=>'佳能']));exit;
//var_dump($product->getSimilarProduct(305821)['data'][0]['saleAttrList']);exit;
//var_dump($product->getProductCategory('653'));exit;//652;654;834
//var_dump($price->getSellPrice([305821]));exit;
//var_dump($stock->getNewStockById([['skuId'=>'305821', 'num'=>1]],['19', '1634', '1640']));exit;
//var_dump($order->getOrderFreight([['skuId'=>'305821', 'num'=>1]],1,19,1634,1640));exit;
//var_dump($order->promiseCalendarNew([['skuId'=>'305821', 'num'=>1]],1,19,1634,1640));exit;
//var_dump($order->orderList('2019-01-01'));exit;
//var_dump($order->checkDeliveredOrder('2019-01-01'));exit;
//var_dump($pay->getUnionBalance('xxx'));exit;
//var_dump($pay->getBalanceDetail());exit;
//var_dump($afterSale->getAvailableNumberComp('40245152920', '800032'));exit;
//var_dump($afterSale->getCustomerExpectComp('40245152920', '800032'));exit;
//var_dump($afterSale->getServiceDetailInfo('40245152920'));exit;
//var_dump($message->getMessage([1]));exit;
