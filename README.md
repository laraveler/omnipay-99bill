# omnipay-99bill

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

基于Omnipay的快钱支付SDK

该文档共包含以下5个部分：  
1、创建交易  
2、支付回调  
3、交易查询  
4、网关退款  

## Quick Start  快速开始

依赖于[Omnipay](https://github.com/omnipay/omnipay) 

PHP版本要求：PHP5.6+


### Install 安装

通过 [Composer](http://getcomposer.org/) 安装，可以运行：

```shell

$ composer require x-class/omnipay-99bill -vvv

```

> 注意：  
> 关于商户号：快钱商家帐号需要联系快钱开通之后才能获取，如果商户号为10012138842，在项目中使用的商户号格式为"快钱商户号01"，如1001213884201，退款接口除外，需要的是不带01的格式。  
> 关于密钥：自行根据快钱文档生成公钥和私钥，其中公钥上传到快钱，然后再从快钱下载官方秘钥放到项目当中，自己生成的私钥则用于创建支付时加密使用。  
> 关于支付验证：支付成功后的同步回调和异步回调均为Get方式，需要对回调的参数进行验证，以确保是快钱官方的回调。  
> 关于订单查询：订单交易状态查询接口除了商户号之外，还需要一个查询key，需要联系快钱的销售人员开通该功能，并获取对应的查询key.  
> 关于退款：网关退款接口除了商户号之外，还需要一个退款key，需要联系快钱的销售人员开通该功能，并获取对应的退款key.


### 开启沙盒调试模式

> 调试过程可以用如下代码开启沙盒环境，正式生产环境请去掉此代码。

```php
$gateway->setTestMode(true);
```

### 创建交易

> 业务字段说明

|字段名|是否必传 |说明|
|:-----  |:-----|-----|
|orderId|Yes |商户订单号,商户可以根据自己订单号的定义规则来定义该值|
|orderAmount|Yes |订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。|
|payerName|No |支付人姓名|
|payerContactType|No |支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式|
|payerContact|No |支付人联系方式，与payerContactType设置对应，payerContactType为1，则填写邮箱地址；payerContactType为2，则填写手机号码|
|productId|No |商品代码|
|productName|No |商品名称|
|productNum|No |商品数量|
|productDesc|No |商品描述|
|ext1|No |扩展字段1，商户可以传递自己需要的参数，支付完快钱会原值返回|
|ext2|No |扩展字段2，商户可以传递自己需要的参数，支付完快钱会原值返回|

> 网站端 - 发起支付代码

```php
$gateway = \Omnipay\Omnipay::create('Bill99');
$gateway->setMchId('mch-id'); //商户号
$gateway->setPrivateKey('private_key'); //私钥内容，可以是文件路径，也可以是内容,如果是内容，保持全在同一行。
$gateway->setReturnUrl('https://www.example.com/return');//支付成功后同步跳转的url
$gateway->setNotifyUrl('https://www.example.com/notify');//支付成功后异步通知url

$request = $gateway->purchase([
    'orderId' => date('YmdHis') . mt_rand(1000, 9999),
    'orderAmount' => 1,
]);

$response = $request->send();
$response->redirect();
exit;
```

> 移动端 - 发起支付代码

```php
$gateway = \Omnipay\Omnipay::create('Bill99');
$gateway->setMchId('mch-id'); //商户号
$gateway->setPrivateKey('private_key'); //私钥内容，可以是文件路径，也可以是内容,如果是内容，保持全在同一行。
$gateway->setReturnUrl('https://www.example.com/return');//支付成功后同步跳转的url
$gateway->setNotifyUrl('https://www.example.com/notify');//支付成功后异步通知url

$request = $gateway->wapPurchase([
    'orderId' => date('YmdHis') . mt_rand(1000, 9999),
    'orderAmount' => 1,
]);

$response = $request->send();
$response->redirect();
exit;
```

### 支付回调

> 回调成功的业务参数

|字段名|说明|
|:-----  |-----|
|orderId|商户订单号 |
|orderAmount|订单金额，金额以“分”为单位|
|orderTime|订单交易创建时间 |
|dealId|快钱交易号 |
|dealTime|交易成功时间 |
|payAmount|实际交易金额，金额以“分”为单位 |
|bankDealId|银行交易号 |
|bankId|银行简码 |
|fee|手续费 |
|bindMobile|绑定的手机号 |
|bindCard|绑定的卡号 |
|payType|支付方式，详见快钱官方文档说明 |
|ext1|扩展字段1 |
|ext2|扩展字段2 |

> 说明：把自己的公钥上传到快钱之后，从快钱下载下来的密钥文件名类似"99bill[1].cert.rsa.20140803.cer"格式。

> 回调验证代码(快钱回调方式为Get)

```php
$gateway = Omnipay::create('Bill99');
$gateway->setMchId('mch-id'); //商户号
$gateway->setPublicKey('99bill_publickey'); //快钱下载的密钥内容，可以是文件路径，也可以是内容,如果是内容，保持全在同一行。
$request = $gateway->completePurchase();
$request->setParams(array_merge($_GET));//获取参数的方法可以用$_GET，也可以用某些框架自带的获取方法，总之要传入url中的Get参数

try {
    $response = $request->send();
    if ($response->isPaid()) {
        $data = $response->getData();//业务参数   
             
        // @todo 支付成功业务逻辑处理，根据返回的业务参数，修改数据库中对应的订单状态
        
        /** 这里需要注意，如果同步回调和同步回调（两者均为GET）在同一处处理，
         * 需要通过一定方式区分是异步还是同步，两者返回信息不同，如用户登录状态
         */
 
        //异步回调值，该返回值为快钱必需
        die($response->asyncResult(true,$redirectUrl)); //成功第一个参数为true，第二个参数为要跳转的url
        //同步回调
        // redirect跳转页面...
    } else {
       // @todo 支付失败的业务逻辑
       //异步回调值，该返回值为快钱必需
       die($response->asyncResult(false)); //失败只需要传入false
    }
} catch (Exception $e) {
    // @todo 这里为支付异常业务逻辑
    //异步回调值，该返回值为快钱必需
   die($response->asyncResult(false)); //失败只需要传入false
}
```

### 查询交易支付状态

> 如果因快钱故障或商户自己服务器故障，成功的交易导致回调失败，则可以用该方法进行查单，通过查单的结果进行数据库状态变更。

> 查询请求业务参数组合一

|字段名|是否必传 |说明|
|:-----  |:-----|-----|
|orderId|Yes |商户订单号|
|queryType|No |查询方式默认为2，即根据商户订单号查询|
|requestPage|No |在查询结果数据总量很大时，快钱会将支付结果分多次返回。本参数表示商户需要得到的记录集页码。默认为1，表示第1 页|

> 查询请求业务参数组合二

|字段名|是否必传 |说明|
|:-----  |:-----|-----|
|queryType|Yes |值固定为1，即根据日期查询|
|startTime|Yes |开始时间,数字串，一共14 位,格式为：年[4 位]月[2 位]日[2 位]时[2 位]分[2 位]秒[2位]，例如：20071117020101|
|endTime|Yes |结束时间,数字串，一共14 位,格式为：年[4 位]月[2 位]日[2 位]时[2 位]分[2 位]秒[2位]，例如：20071117020101|
|requestPage|No |在查询结果数据总量很大时，快钱会将支付结果分多次返回。本参数表示商户需要得到的记录集页码。默认为1，表示第1 页|

> 交易查询 - 实现代码
```php

$gateway = \Omnipay\Omnipay::create('Bill99');
$gateway->setMchId('mch-id'); //商户号
$gateway->setQueryKey('query_key');//交易查询key

//查询方式一
$request = $gateway->query([
	'orderId'  => '201805261456145505',
]);

//查询方式二
/*
$request = $gateway->query([
	'queryType' => 1,
	'startTime' => '20180501000101',
	'endTime'   => '20180527000101',
]);
*/

try {
	$response = $request->send();
	if ($response->isSuccessful()) {
        $data=$response->getData();
        //@todo 支付成功业务逻辑处理，根据返回的业务参数，修改数据库中对应的订单状态
	}else{
	    // @todo 这里为支付异常业务逻辑
	}
} catch (Exception $e) {
    // @todo 这里为支付异常业务逻辑
}

```

### 订单网关退款

> 如未开通网关退款，则需要登录快钱官方商家后台进行退款操作，开通网关退款之后，则可以直接调用此接口退款。

> 警告：此接口为无密退款，因此开发人员需要绝对保证操作后台安全，或加强退款安全验证机制，否则因安全系数太低导致的事故请自行承担。

> 注意一：这里的商户号为不加01的商户号！！！

> 注意二：这里面的退款金额以人民币元为单位！！！

> 注意三：同一笔交易可以分多次退款，但是退款总金额不得超过订单支付总金额

> 退款请求业务参数

|字段名|是否必传 |说明|
|:-----  |:-----|-----|
|orderId|Yes |商户订单号|
|txOrder|Yes |退款流水号,长度不超过50，英文和数字组成|
|amount|Yes |退款金额，整数或小数位为两位，以人民币元为单位。|
|postdate|Yes |退款提交时间，数字串，一共14 位,格式为：年[4 位]月[2 位]日[2 位]时[2 位]分[2 位]秒[2位]，例如：20071117020101|

> 退款成功业务参数

|字段名|说明|
|:-----  |-----|
|orderId|商户订单号 |
|txOrder|退款流水号|
|amount|退款金额|

> 网关退款 - 实现代码
```php
$gateway = \Omnipay\Omnipay::create('Bill99');
$gateway->setMchId('mch-id'); //商户号
$gateway->setRefundKey('refund_key');//退款key

$request = $gateway->refund([
	'orderId'  => 'P270000180502933093',
	'txOrder'  => date('YmdHis'),
	'amount'   => 0.01,
	'postdate' => date('YmdHis'),
]);

try {
	$response = $request->send();
	if ($response->isSuccessful()) {
		$data=$response->getData();
		// @todo 退款成功业务逻辑处理，根据返回的业务参数，修改数据库中对应的订单状态
	}else{
	    // @todo 这里为退款异常业务逻辑
	}
} catch (Exception $e) {
    // @todo 这里为退款异常业务逻辑
}
```

[ico-version]: https://img.shields.io/packagist/v/x-class/omnipay-99bill.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-downloads]: https://img.shields.io/packagist/dt/x-class/omnipay-99bill.svg

[link-packagist]: https://packagist.org/packages/x-class/omnipay-99bill
[link-downloads]: https://packagist.org/packages/x-class/omnipay-99bill
[link-author]: https://github.com/laraveler