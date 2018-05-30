<?php
/**
 * Author 阿伟同学.
 * Date: 2018/5/26
 * Time: 19:06
 */

namespace Omnipay\Bill99\Response;


class RefundQueryResponse extends AbstractResponse
{
	public function isSuccessful()
	{
		$data = $this->data;

		return empty($data['errCode']) || $data['errCode'] == 10012 ? true : false;
	}

	public function getData()
	{
		$data = $this->data;

		return $data['results'];
	}
}