<?php

namespace Omnipay\Bill99\Request;

use Omnipay\Common\Message\AbstractRequest as Request;

/**
 * Class AbstractRequest
 * @package Omnipay\Bill99\Request
 * @author laraveler <happyjkw2005@gmail.com>
 */
abstract class AbstractRequest extends Request
{
    protected $method = 'POST';
    protected $privateKey;
    protected $publicKey;

    protected $productionEndpoint = '';
    protected $sandBoxEndpoint = '';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->sandBoxEndpoint : $this->productionEndpoint;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setPublicKey($value)
    {
        $this->publicKey = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->getParameter('orderTime');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTimestamp($value)
    {
        return $this->setParameter('orderTime', $value);
    }

    protected function setDefaults()
    {
        if (!$this->getTimestamp()) {
            $this->setTimestamp(date('YmdHis'));
        }
    }

    protected function convertToString()
    {
        foreach ($this->parameters->all() as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $this->parameters->set($key, json_encode($value));
            }
        }
    }

    /**
     * 人民币网关账号，该账号为11位人民币网关商户编号+01,该参数必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setMerchantAcctId($value)
    {
        return $this->setParameter('merchantAcctId', $value);
    }

    /**
     * 编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setInputCharset($value)
    {
        return $this->setParameter('inputCharset', $value);
    }

    /**
     * 接收支付结果的页面地址，该参数一般置为空即可。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPageUrl($value)
    {
        return $this->setParameter('pageUrl', $value);
    }

    /**
     * 服务器接收支付结果的后台地址，该参数务必填写，不能为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setBgUrl($value)
    {
        return $this->setParameter('bgUrl', $value);
    }

    /**
     * 网关版本，固定值：v2.0,该参数必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setVersion($value)
    {
        return $this->setParameter('version', $value);
    }

    /**
     * 语言种类，1代表中文显示，2代表英文显示。默认为1,该参数必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * 签名类型,该值为4，代表PKI加密方式,该参数必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setSignType($value)
    {
        return $this->setParameter('signType', $value);
    }

    /**
     * 支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10，必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPayType($value)
    {
        return $this->setParameter('payType', $value);
    }

    /**
     * 银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setBankId($value)
    {
        return $this->setParameter('bankId', $value);
    }

    /**
     * 同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setRedoFlag($value)
    {
        return $this->setParameter('redoFlag', $value);
    }

    /**
     * 快钱合作伙伴的帐户号，即商户编号，可为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPid($value)
    {
        return $this->setParameter('pid', $value);
    }

    /**
     * 支付人姓名,可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPayerName($value)
    {
        return $this->setParameter('payerName', $value);
    }

    /**
     * 支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式。可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPayerContactType($value)
    {
        return $this->setParameter('payerContactType', $value);
    }

    /**
     * 支付人联系方式，与payerContactType设置对应，payerContactType为1，则填写邮箱地址；payerContactType为2，则填写手机号码。可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPayerContact($value)
    {
        return $this->setParameter('payerContact', $value);
    }

    /**
     * 商户订单号，以下采用时间来定义订单号，商户可以根据自己订单号的定义规则来定义该值，不能为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * 订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。该参数必填。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setOrderAmount($value)
    {
        return $this->setParameter('orderAmount', $value);
    }

    /**
     * 商品名称，可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setProductName($value)
    {
        return $this->setParameter('productName', $value);
    }

    /**
     * 商品数量，可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setProductNum($value)
    {
        return $this->setParameter('productNum', $value);
    }

    /**
     * 商品代码，可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    /**
     * 商品描述，可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setProductDesc($value)
    {
        return $this->setParameter('productDesc', $value);
    }

    /**
     * 展字段1，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setExt1($value)
    {
        return $this->setParameter('ext1', $value);
    }

    /**
     * 扩展自段2，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setExt2($value)
    {
        return $this->setParameter('ext2', $value);
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setPrivateKey($value)
    {
        $this->privateKey = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignType()
    {
        return $this->getParameter('signType');
    }
}
