<?php

namespace Omnipay\Bill99;

use Omnipay\Bill99\Request\PurchaseRequest;
use Omnipay\Bill99\Request\CompletePurchaseRequest;
use Omnipay\Bill99\Request\QueryRequest;
use Omnipay\Bill99\Request\RefundQueryRequest;
use Omnipay\Bill99\Request\RefundRequest;
use Omnipay\Bill99\Request\WapPurchaseRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Class Gateway
 * @package Omnipay\Bill99
 * @author laraveler <happyjkw2005@gmail.com>
 */
class Gateway extends AbstractGateway
{
	public function getName()
	{
		return '99Bill Gateway';
	}

	public function getDefaultParameters()
	{
		return [
			//编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
			'inputCharset' => '1',
			//网关版本，固定值：v2.0,该参数必填。
			'version'      => 'v2.0',
			//语言种类，1代表中文显示，2代表英文显示。默认为1,该参数必填。
			'language'     => 1,
			//签名类型,该值为4，代表PKI加密方式,该参数必填。
			'signType'     => '4',
			//支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10，必填。
			'payType'      => '00',
			//银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
			'bankId'       => '',
			//同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
			'redoFlag'     => '0',
			//快钱合作伙伴的帐户号，即商户编号，可为空。
			'pid'          => '',
		];
	}

	/**
	 * @return mixed
	 */
	public function getPublicKey()
	{
		return $this->getParameter('public_key');
	}


	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setPublicKey($value)
	{
		return $this->setParameter('public_key', $value);
	}

	public function setMchId($mchId)
	{
		$this->setParameter('merchantAcctId', $mchId);
	}

	public function getMchId()
	{
		return $this->getParameter('merchantAcctId');
	}

	/**
	 * @return mixed
	 */
	public function getReturnUrl()
	{
		return $this->getParameter('pageUrl');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setReturnUrl($value)
	{
		return $this->setParameter('pageUrl', $value);
	}

	/**
	 * @return mixed
	 */
	public function getNotifyUrl()
	{
		return $this->getParameter('bgUrl');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setNotifyUrl($value)
	{
		return $this->setParameter('bgUrl', $value);
	}

	/**
	 * @return mixed
	 */
	public function getLanguage()
	{
		return $this->getParameter('language');
	}

	/**
	 * @param $language
	 * @return $this
	 */
	public function setLanguage($language)
	{
		return $this->setParameter('language', $language);
	}

	/**
	 * @return mixed
	 */
	public function getPrivateKey()
	{
		return $this->getParameter('private_key');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setPrivateKey($value)
	{
		return $this->setParameter('private_key', $value);
	}


	/**
	 * @return mixed
	 */
	public function getQueryKey()
	{
		return $this->getParameter('query_key');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setQueryKey($value)
	{
		return $this->setParameter('query_key', $value);
	}

	/**
	 * @return mixed
	 */
	public function getRefundKey()
	{
		return $this->getParameter('refund_key');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setRefundKey($value)
	{
		return $this->setParameter('refund_key', $value);
	}


	/**
	 * @param array $parameters
	 * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 */
	public function purchase(array $parameters = [])
	{
		return $this->createRequest(PurchaseRequest::class, $parameters);
	}


	/**
	 * @param array $parameters
	 * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 */
	public function wapPurchase(array $parameters = [])
	{
		return $this->createRequest(WapPurchaseRequest::class, $parameters);
	}


	/**
	 * @param array $parameters
	 * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 */
	public function query(array $parameters = [])
	{
		return $this->createRequest(QueryRequest::class, $parameters);
	}


	/**
	 * @param array $parameters
	 * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 */
	public function refund(array $parameters = [])
	{
		return $this->createRequest(RefundRequest::class, $parameters);
	}


	/**
	 * @param array $parameters
	 * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 */
	public function refundQuery(array $parameters = [])
	{
		return $this->createRequest(RefundQueryRequest::class, $parameters);
	}

	/**
	 * @param array $parameters
	 * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 */
	public function completePurchase(array $parameters = [])
	{
		return $this->createRequest(CompletePurchaseRequest::class, $parameters);
	}
}
