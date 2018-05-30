<?php

namespace Omnipay\Bill99\Request;

use Omnipay\Bill99\Common\Signer;
use Omnipay\Bill99\Response\PurchaseResponse;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class PurchaseRequest
 * @package Omnipay\Bill99\Request
 * @author laraveler <happyjkw2005@gmail.com>
 */
class PurchaseRequest extends AbstractRequest
{
	protected $productionEndpoint = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm';
	protected $sandBoxEndpoint = 'https://sandbox.99bill.com/gateway/recvMerchantInfoAction.htm';

	public function getData()
	{
		$this->validateParams();
		$this->setDefaults();
		$this->convertToString();
		$data = $this->parameters->all();
		$data['signMsg'] = $this->sign($this->sortRequest($data));

		return $data;
	}

	/**
	 * @throws \Omnipay\Common\Exception\InvalidRequestException
	 */
	public function validateParams()
	{
		$this->validate(
			'merchantAcctId',
			'pageUrl',
			'bgUrl',
			'orderId',
			'orderAmount'
		);
	}

	public function sendData($data)
	{
		return $this->response = new PurchaseResponse($this, $data);
	}

	protected function sign($params)
	{
		if (! $this->getPrivateKey()) {
			throw new InvalidRequestException('The `ssl_cert` is required');
		}
		$signer = new Signer($params);
		$signer->setIgnores([ 'signMsg' ]);
		$sign = $signer->signWithRSA($this->getPrivateKey());

		return $sign;
	}

	/**
	 * 该参数用于加密签名及创建form表单，参数顺序不能改变，否则会引起签名错误。
	 * @param $params
	 * @return array
	 */
	protected function sortRequest($params)
	{
		return [
			'inputCharset'     => $params['inputCharset'],
			'pageUrl'          => isset($params['pageUrl']) ? $params['pageUrl'] : '',
			'bgUrl'            => isset($params['bgUrl']) ? $params['bgUrl'] : '',
			'version'          => $params['version'],
			'language'         => $params['language'],
			'signType'         => $params['signType'],
			'merchantAcctId'   => isset($params['merchantAcctId']) ? $params['merchantAcctId'] : '',
			'payerName'        => isset($params['payerName']) ? $params['payerName'] : '',
			'payerContactType' => isset($params['payerContactType']) ? $params['payerContactType'] : '',
			'payerContact'     => isset($params['payerContact']) ? $params['payerContact'] : '',
			'payerIdType'      => isset($params['payerIdType']) ? $params['payerIdType'] : '',
			'payerId'          => isset($params['payerId']) ? $params['payerId'] : '',
			'payerIP'          => isset($params['payerIP']) ? $params['payerIP'] : '',
			'orderId'          => isset($params['orderId']) ? $params['orderId'] : '',
			'orderAmount'      => isset($params['orderAmount']) ? $params['orderAmount'] : 0,
			'orderTime'        => isset($params['orderTime']) ? $params['orderTime'] : '',
			'orderTimestamp'   => isset($params['orderTimestamp']) ? $params['orderTimestamp'] : '',
			'productName'      => isset($params['productName']) ? $params['productName'] : '',
			'productNum'       => isset($params['productNum']) ? $params['productNum'] : '',
			'productId'        => isset($params['productId']) ? $params['productId'] : '',
			'productDesc'      => isset($params['productDesc']) ? $params['productDesc'] : '',
			'ext1'             => isset($params['ext1']) ? $params['ext1'] : '',
			'ext2'             => isset($params['ext2']) ? $params['ext2'] : '',
			'payType'          => isset($params['payType']) ? $params['payType'] : '',
			'bankId'           => isset($params['bankId']) ? $params['bankId'] : '',
			'cardIssuer'       => isset($params['cardIssuer']) ? $params['cardIssuer'] : '',
			'cardNum'          => isset($params['cardNum']) ? $params['cardNum'] : '',
			'remitType'        => isset($params['remitType']) ? $params['remitType'] : '',
			'remitCode'        => isset($params['remitCode']) ? $params['remitCode'] : '',
			'redoFlag'         => isset($params['redoFlag']) ? $params['redoFlag'] : '',
			'pid'              => isset($params['pid']) ? $params['pid'] : '',
			'submitType'       => isset($params['submitType']) ? $params['submitType'] : '',
			'orderTimeOut'     => isset($params['orderTimeOut']) ? $params['orderTimeOut'] : '',
			'extDataType'      => isset($params['extDataType']) ? $params['extDataType'] : '',
			'extDataContent'   => isset($params['extDataContent']) ? $params['extDataContent'] : '',
		];
	}
}
