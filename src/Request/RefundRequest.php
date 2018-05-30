<?php
/**
 * Author 阿伟同学.
 * Date: 2018/5/26
 * Time: 19:01
 */

namespace Omnipay\Bill99\Request;


use Omnipay\Bill99\Response\RefundResponse;
use Omnipay\Common\Exception\InvalidRequestException;

class RefundRequest extends AbstractRequest
{
	protected $refundKey;
	protected $version = 'bill_drawback_api_2';
	protected $command_type = '001';
	protected $productionEndpoint = 'https://www.99bill.com/webapp/receiveDrawbackAction.do';
	protected $sandBoxEndpoint = 'https://sandbox.99bill.com/webapp/receiveDrawbackAction.do';

	/**
	 * @return mixed
	 */
	public function getRefundKey()
	{
		return $this->refundKey;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setRefundKey($value)
	{
		$this->refundKey = $value;

		return $this;
	}

	public function setTxOrder($value)
	{
		return $this->setParameter('txOrder', $value);
	}

	public function setPostdate($value)
	{
		return $this->setParameter('postdate', $value);
	}

	public function setOrderid($value)
	{
		return $this->setParameter('orderid', $value);
	}


	public function getData()
	{
		try {
			$this->setVersion($this->version);
			$this->setParameter('command_type', $this->command_type);
			$this->validateParams();
		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage());
		}

		$data = $this->sortRequest($this->parameters->all());
		$data['mac'] = $this->sign($data);

		return $data;
	}


	/**
	 * @throws \Omnipay\Common\Exception\InvalidRequestException
	 */
	public function validateParams()
	{
		$this->validate(
			'merchantAcctId',
			'version',
			'command_type',
			'txOrder',
			'amount',
			'postdate',
			'orderid'
		);
	}


	public function sendData($data)
	{
		try {
			$url = sprintf('%s?%s', $this->getEndpoint(), http_build_query($data));
			$result = $this->httpClient->request('get', $url)->getBody();
			$data = xml2array($result);

			return $this->response = new RefundResponse($this, $data);

		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage());
		}


	}

	protected function sign($params)
	{
		if (!$this->getRefundKey()) {
			throw new InvalidRequestException('The `refund_key` is required');
		}

		$content = '';
		foreach ($params as $key => $value) {
			$content .= $key . '=' . $value;
		}
		$content .= 'merchant_key=' . $this->getRefundKey();

		$sign = strtoupper(md5($content));

		return $sign;
	}

	/**
	 * 该参数用于加密签名，参数顺序不能改变，否则会引起签名错误。
	 * @param $params
	 * @return array
	 */
	protected function sortRequest($params)
	{
		return [
			'merchant_id'  => isset($params['merchantAcctId']) ? $params['merchantAcctId'] : '',
			'version'      => $params['version'],
			'command_type' => isset($params['queryType']) ? $params['queryType'] : '001',
			'orderid'      => isset($params['orderid']) ? $params['orderid'] : '',
			'amount'       => isset($params['amount']) ? $params['amount'] : '',
			'postdate'     => isset($params['postdate']) ? $params['postdate'] : '',
			'txOrder'      => isset($params['txOrder']) ? $params['txOrder'] : '',
		];
	}
}