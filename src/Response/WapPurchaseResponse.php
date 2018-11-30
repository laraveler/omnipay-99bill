<?php

namespace Omnipay\Bill99\Response;

use Omnipay\Common\Message\RedirectResponseInterface;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class WapPurchaseResponse
 * @package Omnipay\Bill99\Response
 * @author laraveler <happyjkw2005@gmail.com>
 */
class WapPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $request;

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->request->getEndpoint() . '?' . http_build_query($this->getRedirectData());
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        return $this->getData();
    }


    /**
     * @return HttpRedirectResponse|HttpResponse|static
     */
    public function getRedirectResponse()
    {
        if (!$this instanceof RedirectResponseInterface || !$this->isRedirect()) {
            throw new \RuntimeException('This response does not support redirection.');
        }

        if ('GET' === $this->getRedirectMethod()) {
            return HttpRedirectResponse::create($this->getRedirectUrl());
        } elseif ('POST' === $this->getRedirectMethod()) {
            $hiddenFields = '';
            foreach ($this->getRedirectData() as $key => $value) {
                $hiddenFields .= sprintf(
                        '<input type="hidden" name="%s" value="%s" />',
                        htmlentities($key, ENT_QUOTES, 'UTF-8', false),
                        htmlentities($value, ENT_QUOTES, 'UTF-8', false)
                    ) . "\n";
            }

            $output = '<!DOCTYPE html>
						<html>
							<head>
								<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
								<title>Redirecting...</title>
							</head>
							<body onload="document.forms[0].submit();">
								<form action="%s" method="post">
									%s
									<input style="display: none;" type="submit" value="Continue" />
								</form>
							</body>
						</html>';
            $output = sprintf(
                $output,
                htmlentities($this->request->getEndpoint(), ENT_QUOTES, 'UTF-8', false),
                $hiddenFields
            );

            return HttpResponse::create($output);
        }

        throw new \RuntimeException('Invalid redirect method "' . $this->getRedirectMethod() . '".');
    }
}
