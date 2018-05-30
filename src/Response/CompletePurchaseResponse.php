<?php

namespace Omnipay\Bill99\Response;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class CompletePurchaseResponse
 * @package Omnipay\Bill99\Response
 * @author laraveler <happyjkw2005@gmail.com>
 */
class CompletePurchaseResponse extends AbstractResponse
{
	public function isSuccessful()
	{
		return true;
	}

	public function isPaid()
	{
		$data = $this->data;
		if ($data['paid']) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 异步验证返回的结果，快钱要求的固定格式,不可改变.
	 * @param bool $result
	 * @param $url
	 * @return string
	 */
	public function asyncResult($result = true, $redirectUrl = '')
	{
		if ($result == true) {
			return "<result>1</result><redirecturl>{$redirectUrl}</redirecturl>";
		} else {
			return "<result>0</result><redirecturl>{$redirectUrl}</redirecturl>";
		}
	}
}
