<?php

namespace Omnipay\Bill99\Request;

use Omnipay\Bill99\Common\Signer;
use Omnipay\Bill99\Response\CompletePurchaseResponse;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class CompletePurchaseRequest
 * @package Omnipay\Bill99\Request
 * @author laraveler <happyjkw2005@gmail.com>
 */
class CompletePurchaseRequest extends AbstractRequest
{
	/**
	 * @return mixed
	 */
	public function getData()
	{
		$this->validateParams();

		return $this->getParams();
	}


	public function validateParams()
	{
		$this->validate('params');
	}

	/**
	 * @return mixed
	 */
	public function getParams()
	{
		return $this->getParameter('params');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setParams($value)
	{
		return $this->setParameter('params', $value);
	}

	public function sendData($data)
	{
		$match = $this->sign($this->sortReturn($data), $data['signMsg']);
		if ($match && isset($data['payResult']) && $data['payResult'] == '10') {
			$data['paid'] = true;
		} else {
			$data['paid'] = false;
		}

		return $this->response = new CompletePurchaseResponse($this, $data);
	}

	protected function sign($params, $sign)
	{
		if (! $this->getPublicKey()) {
			throw new InvalidRequestException('The `ssl_key` is required');
		}
		$signer = new Signer($params);
		$content = $signer->getContentToSign();
		$match = (new Signer())->verifyWithRSA($content, $sign, $this->getPublicKey());

		return $match;
	}

	/**
	 * 对快钱返回的参数进行签名验证，参数顺序不能改变，否则会引起签名错误。
	 * @param $params
	 * @return array
	 */
	protected function sortReturn($params)
	{
		return [
			'merchantAcctId' => isset($params['merchantAcctId']) ? $params['merchantAcctId'] : '',
			'version'        => isset($params['version']) ? $params['version'] : '',
			'language'       => isset($params['language']) ? $params['language'] : '',
			'signType'       => isset($params['signType']) ? $params['signType'] : '',
			'payType'        => isset($params['payType']) ? $params['payType'] : '',
			'bankId'         => isset($params['bankId']) ? $params['bankId'] : '',
			'orderId'        => isset($params['orderId']) ? $params['orderId'] : '',
			'orderTime'      => isset($params['orderTime']) ? $params['orderTime'] : '',
			'orderAmount'    => isset($params['orderAmount']) ? $params['orderAmount'] : '',
			'bindCard'       => isset($params['bindCard']) ? $params['bindCard'] : '',
			'bindMobile'     => isset($params['bindMobile']) ? $params['bindMobile'] : '',
			'dealId'         => isset($params['dealId']) ? $params['dealId'] : '',
			'bankDealId'     => isset($params['bankDealId']) ? $params['bankDealId'] : '',
			'dealTime'       => isset($params['dealTime']) ? $params['dealTime'] : '',
			'payAmount'      => isset($params['payAmount']) ? $params['payAmount'] : '',
			'fee'            => isset($params['fee']) ? $params['fee'] : '',
			'ext1'           => isset($params['ext1']) ? $params['ext1'] : '',
			'ext2'           => isset($params['ext2']) ? $params['ext2'] : '',
			'payResult'      => isset($params['payResult']) ? $params['payResult'] : '',
			'errCode'        => isset($params['errCode']) ? $params['errCode'] : '',
		];
	}
}
