<?php

namespace Yay\Core\Response;

use Yay\Core\Collection\Map;
use Yay\Core\Exception\ArgumentMismatchException;

/**
 * Base class for response classes. It extends Map, so we can navigate through it's elements
 * easily.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Response
 */
abstract class Response extends Map implements iResponse
{
	private $_statusCode = HttpStatusCodes::StatusOk;
	private $_mimeType = 'text/plain';

	public function setStatusCode($statusCode)
	{
		if (!is_numeric($statusCode))
			throw new ArgumentMismatchException("Can't set invalid status code (must be numeric).");

		$this->_statusCode = $statusCode;
		http_response_code($this->_statusCode);
	}

	public function statusCode()
	{
		return $this->_statusCode;
	}

	public function setMimeType($mimeType)
	{
		$this->_mimeType = $mimeType;
	}

	public function mimeType()
	{
		return $this->_mimeType;
	}
}