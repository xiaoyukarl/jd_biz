####京东大客户开发平台VOP对接
##### 使用方法
- 下载此包 `composer require xiaoyukarl/jd_biz`
- 修改配置文件config.php
- 调用接口方法
    ```
    //使用地址接口
    $address = new \jd\biz\request\AddressRequest();
    //获取省份列表
    var_dump($address->getProvinces());exit;
    ```

##### config.php配置文件说明
```
return [
    'client_id' => '', //对接client_id
    'client_secret' => '',//对接账号密码
    'username' => '',//京东用户名,可能是中文
    'password' => '',//京东的密码
    'jdBaseUrl' => 'https://bizapi.jd.com/',
];
```

##### 说明
- 接口返回数据格式
```
[
    'code' => 0, //当code等于0时,代表接口请求成功
    'message' => 'success', //当接口请求发生错误时, 显示错误信息
    'data' => [],//具体返回参数,
]
```

- 接口提供方法
```
AddressRequest.php 地址API包含方法
[
    查询一级地址 getProvinces()
    查询二级地址 getCity($provinceId)
    查询三级地址 getCounty($cityId)
    查询四级地址 getTown($countyId)
    验证地址有效性 checkArea($address)
    地址详情转换京东地址编码 getJDAddressFromAddress($address)
]
ProductRequest.php 商品API包含方法
[
    查询所有商品池编号，商品池编号将用于获取池内商品编号 getProductPool()
    查询单个商品池下的商品列表 getSkuByPage($pageNum, $pageNo)
    查询单个商品的详细信息 getDetail($sku, $queryExts)
    查询单个商品的主图、轮播图 getImage($skuIds)
    查询商品的上下架状态 saleStatus($skuIds)
    查询商品可售性、是否支持专票等影响销售的重要属性 checkProduct($skuIds)
    查询商品在特定区域是否可售 checkProductAreaLimit($skuIds, $province, $city, $county, $town)
    根据此接口查询主商品附带的赠品 getSkuGift($skuId, $province, $city, $county, $town)
    根据此接口查询可随主商品一并购买的延保等服务商品 getYanbaoSku($skuIds, $province, $city, $county, $town)
    验证商品在指定区域是否可使用货到付款 checkCashOnDelivery($skuIds, $province, $city, $county, $town)
    批量验证商品在指定区域是否可使用货到付款 atchCheckCashOnDelivery($skuIds, $province, $city, $county, $town)
    根据搜索条件查询符合要求的商品列表 searchProduct($params)
    查询被指定为同一类的商品 getSimilarProduct($skuId)
    根据分类id查询对应分类信息 getProductCategory($categoryId)
]

StockRequest.php 库存API包含方法
[
    批量获取库存接口 getNewStockById($skuNums, $area)
]

PriceRequest.php 商品价格API包含方法
[
    批量查询商品售卖价  getSellPrice($skuIds, $queryExts)
]

BalanceRequest.php 余额支付API包含方法
[
    查询金采和预存款余额的余额 getUnionBalance($type)
    仅支持预存款余额明细查询，不支持金采余额明细查询 getBalanceDetail($pageNum = 1, $pageSize = 20, $orderNum = '', $startDate = '', $endDate = '')
    下单成功支付失败的情况，可以调用此接口重新支付 payOrder($jdOrderNum)
]

OrderRequest.php 订单API包含方法
[
    查询准备提交的订单的运费 getOrderFreight($skuNums, $paymentType, $province, $city, $county, $town)
    获取京东预约日历 promiseCalendarNew($skuNums, $paymentType, $province, $city, $county, $town)
    提交订单信息，生成京东订单 submitOrder(array $order)
    订单反查接口，根据第三方订单号反查京东的订单号  getJDOrder($orderNum)
    确认预占库存订单接口 confirmOrder($jdOrderNum)
    取消未确认订单接口 cancelOrder($jdOrderNum)
    查询京东订单信息接口 orderDetail($jdOrderNum, $queryExts = [])
    查询配送信息 orderTrack($jdOrderNum, $waybillCode = 1)
    确认收货 confirmReceivedOrder($jdOrderNum)
    更新采购单号 saveOrUpdatePoNo($jdOrderNum, $poNo)
    查询所有新建的订单列表。可用于核对订单 orderList($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    查询所有妥投的订单列表。可用于核对订单 checkDeliveredOrder($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    查询所有拒收的订单列表。可用于核对订单 checkRefuseOrder($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    查询所有完成的订单列表。可用于核对订单 checkCompleteOrder($startDate, $pageNo = 1, $pageSize = 20, $endDate = '', $jdOrderIdIndex = '')
    查询配送预计送达时间  getPromiseTips($skuId, $num, $province, $city, $county, $town = 0)
]

AfterSaleRequest.php    售后服务API
[
    校验某订单中某商品是否可以提交售后服务 getAvailableNumberComp($jdOrderNum, $skuId)
    根据订单号、商品编号查询支持的服务类型 getCustomerExpectComp($jdOrderNum, $skuId)
    根据订单号、商品编号查询支持的商品返回京东方式 getWareReturnJdComp($jdOrderNum, $skuId)
    发起售后申请  createAfsApply($params)
    填写发运信息  updateSendSku($afsServiceId, $expressCompany, $deliverDate, $expressCode, $freightMoney = 0.0)
    查询订单下服务单汇总信息   getServiceListPage($jdOrderNum, $pageIndex = 1, $pageSize = 20)
    查询服务单明细信息。  getServiceDetailInfo($afsServiceId, $appendInfoSteps = [1,2,3,4,5])
    取消已经生成的服务单  auditCancel(array $serviceIdList, $approveNotes)
    确认服务单   confirmAfsOrder($customerName, $username, $afsServiceId)
    查询订单下服务单汇总列表信息。 getAfsServiceListPage(array $query = ['pageIndex' => 1, 'pageSize' => 20])
]

InvoiceRequest.php    发票API
[
    申请开票    createInvoice(array $invoice)
    通过的订单号查询对应的第三方申请单号  queryThrApplyNo($jdOrderNum)
    查询第三方申请单号下的发票概要信息   queryInvoiceDesc($markId)
    查询发票明细信息。目前只支持纸质发票  queryInvoiceItem($invoiceId, $invoiceCode)
    查询电子发票明细信息  getInvoiceList($jdOrderNum, $invoiceType, $queryExts = ['prefixZero', 'electronicVAT'])
    纸质发票如果需要邮寄，使用此接口查询配送单号  getInvoiceWaybill($markId)
    查询发票物流消息信息  queryDeliveryNo($jdOrderNum)
    取消已经提交的开票申请     cancelInvoice($markId)
]

MessageRequest.php     消息服务API
[
    获取推送信息接口    getMessage($type)
    根据推送id，删除推送信息接口 delMessage( array $ids)
]
    
```


##### 目录结构
```
jd_biz
├─ README.md
├─ composer.json
├─ composer.lock
├─ src 
│  ├─ configs
│  │  └─ config.php 配置文件
│  ├─ lib
│  │  ├─ ConfigLib.php  获取配置文件
│  │  ├─ CurlLib.php    curl
│  │  ├─ HelperLib.php    帮助类
│  │  ├─ QueryTools.php    curl工具类
│  │  └─ TokenLib.php   获取token工具类
│  ├─ request
│  │  ├─ AddressRequest.php     地址接口请求  
│  │  ├─ AfterSaleRequest.php     售后接口请求
│  │  ├─ BalanceRequest.php     余额和支付接口请求
│  │  ├─ InvoiceRequest.php     发票接口请求
│  │  ├─ MessageRequest.php     信息接口请求
│  │  ├─ OrderRequest.php       订单接口请求
│  │  ├─ PriceRequest.php       商品价格接口请求
│  │  ├─ ProductRequest.php     产品接口请求
│  │  ├─ StockRequest.php    库存接口请求
│  │  └─ Request.php   接口请求父类
│  └─ storage token缓存目录
├─ test
│  └─ test.php 测试文件
└─ vendor 自动加载目录
```