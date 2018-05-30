<?php
/**
 * Author 阿伟同学.
 * Date: 2018/5/26
 * Time: 19:07
 */

namespace Omnipay\Bill99\Response;


class RefundResponse extends AbstractResponse
{
	public function isSuccessful()
	{
		$data = $this->data;

		return $data['RESULT'] == 'Y' ? true : false;
	}

	public function getData()
	{
		$data = $this->data;

		return [
			'orderId' => $data['ORDERID'],
			'txOrder' => $data['TXORDER'],
			'amount'   => $data['AMOUNT'],
		];
	}
}