<?php

namespace SS6\ShopBundle\Component\HttpFoundation\Exception;

use Exception;
use SS6\ShopBundle\Component\HttpFoundation\Exception\HttpFoundationException;

class TooManyRedirectResponsesException extends Exception implements HttpFoundationException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
