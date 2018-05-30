<?php
/**
 * Author 阿伟同学.
 * Date: 2018/5/27
 * Time: 01:07
 */

if (!function_exists('object_array')) {
	function object_array($array)
	{
		if (is_object($array)) {
			$array = (array)$array;
		}
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				$array[$key] = object_array($value);
			}
		}

		return $array;
	}
}

if (!function_exists('xml2array')) {
	function xml2array($xml)
	{
		return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	}
}