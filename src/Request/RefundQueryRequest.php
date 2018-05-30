<?php
/**
 * Author 阿伟同学.
 * Date: 2018/5/26
 * Time: 19:01
 */

namespace Omnipay\Bill99\Request;


use Omnipay\Bill99\Common\Signer;
use Omnipay\Bill99\Response\RefundQueryResponse;
use Omnipay\Common\Exception\InvalidRequestException;

class RefundQueryRequest extends AbstractRequest
{
	protected $queryKey;
	protected $signType = 1;
	protected $productionEndpoint = 'https://www.99bill.com/gatewayapi/services/gatewayRefundQuery?wsdl';
	protected $sandBoxEndpoint = 'https://sandbox.99bill.com/gatewayapi/services/gatewayRefundQuery?wsdl';

	/**
	 * @return mixed
	 */
	public function getQueryKey()
	{
		return $this->queryKey;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setQueryKey($value)
	{
		$this->queryKey = $value;

		return $this;
	}

	public function setQueryType($value)
	{
		return $this->setParameter('queryType', $value);
	}

	public function setStartDate($value)
	{
		return $this->setParameter('startDate', $value);
	}

	public function setEndDate($value)
	{
		return $this->setParameter('endDate', $value);
	}

	public function setRequestPage($value)
	{
		return $this->setParameter('requestPage', $value);
	}

	public function setStatus($value)
	{
		return $this->setParameter('status', $value);
	}


	public function getData()
	{
		$this->validateParams();

		$this->setSignType($this->signType);
		$data = $this->sortRequest($this->parameters->all());
		$data['signMsg'] = $this->sign($data);

		return $data;
	}


	/**
	 * @throws \Omnipay\Common\Exception\InvalidRequestException
	 */
	public function validateParams()
	{
		$this->validate(
			'merchantAcctId'
		);
	}


	public function sendData($data)
	{
		try {
			$clientObj = new \SoapClient($this->getEndpoint());
			$result = $clientObj->__soapCall('query', [ $data ]);

			$data = object_array($result);

			return $this->response = new RefundQueryResponse($this, $data);

		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage());
		}


	}

	protected function sign($params)
	{
		if (! $this->getQueryKey()) {
			throw new InvalidRequestException('The `query_key` is required');
		}
		$signer = new Signer($params);
		$signer->setIgnores([ 'signMsg' ]);
		$content = $signer->getContentToSign() . '&key=' . $this->getQueryKey();
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
			'version'             => $params['version'],
			'signType'            => $params['signType'],
			'merchantAcctId'      => isset($params['merchantAcctId']) ? $params['merchantAcctId'] : '',
			'startDate'           => isset($params['startDate']) ? $params['startDate'] : '',
			'endDate'             => isset($params['endDate']) ? $params['endDate'] : '',
			'lastUpdateStartDate' => isset($params['lastUpdateStartDate']) ? $params['lastUpdateStartDate'] : '',
			'lastUpdateEndDate'   => isset($params['lastUpdateEndDate']) ? $params['lastUpdateEndDate'] : '',
			'customerBatchId'     => isset($params['customerBatchId']) ? $params['customerBatchId'] : '',
			'orderId'             => isset($params['orderId']) ? $params['orderId'] : '',
			'requestPage'         => isset($params['requestPage']) ? $params['requestPage'] : 1,
			'rOrderId'            => isset($params['rOrderId']) ? $params['rOrderId'] : '',
			'seqId'               => isset($params['seqId']) ? $params['seqId'] : '',
			'extra_output_column' => isset($params['extra_output_column']) ? $params['extra_output_column'] : '',
			'status'              => isset($params['status']) ? $params['status'] : '',
		];
	}
}