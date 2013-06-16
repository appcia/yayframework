<?php

namespace Yay\Core\Request;

use Yay\Core\yComponent;
use Yay\Core\Exception;

/**
 * A class that represents a request.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Request
 */
class Request extends yComponent
{
	const MethodGet = 1;
	const MethodPost = 2;
	const MethodPut = 3;
	const MethodDelete = 4;
	const MethodOptions = 5;
	const MethodHead = 6;
	const MethodTrace = 7;
	const MethodConnect = 8;

	/**
	 * @var int
	 */
	private $_httpMethod;
	/**
	 * @var string
	 */
	private $_httpMethodString;

	/**
	 * Gets the request uri.
	 *
	 * @return string
	 */
	public function uri()
	{
		return $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : '/';
	}

	/**
	 * Gets the domain name.
	 *
	 * @return string
	 */
	public function domainName()
	{
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	}

	/**
	 * Gets the http method as int (see class constants).
	 *
	 * @return int
	 */
	public function httpMethod()
	{
		if (!$this->_httpMethodString)
		{
			$this->_httpMethodString = strtolower($_SERVER['REQUEST_METHOD']);
			$this->_httpMethod = $this->getHttpMethodFromString($this->_httpMethodString);
		}

		return $this->_httpMethod;
	}

	/**
	 * Gets the http method as lowercase string.
	 *
	 * @return string
	 */
	public function httpMethodString()
	{
		$this->httpMethod();
		return $this->_httpMethodString;
	}

	/**
	 * Sets the http method. Usable if you want to alter the request. See class constants for possible values.
	 *
	 * @param int $method
	 * @throws \Yay\Core\Exception\ArgumentMismatchException
	 */
	public function setHttpMethod($method)
	{
		if (!is_numeric($method) || $method < 1 || $method > 8)
			throw new Exception\ArgumentMismatchException("You should set http method with Request class constants.");
		
		$this->_httpMethod = $method;
	}

	/**
	 * Gets the http method as int from a http method string. See class constants for possible values.
	 *
	 * @param string $methodString
	 * @return int
	 * @throws \Yay\Core\Exception\ArgumentMismatchException
	 */
	public function getHttpMethodFromString($methodString)
	{
		$methodString = strtolower($methodString);
		if ($methodString == 'get')
			return self::MethodGet;
		else if ($methodString == 'post')
			return self::MethodPost;
		else if ($methodString == 'put')
			return self::MethodPut;
		else if ($methodString == 'delete')
			return self::MethodDelete;
		else if ($methodString == 'options')
			return self::MethodOptions;
		else if ($methodString == 'head')
			return self::MethodHead;
		else if ($methodString == 'trace')
			return self::MethodTrace;
		else if ($methodString == 'connect')
			return self::MethodConnect;
		
		throw new Exception\ArgumentMismatchException("Invalid http method string specified.");
	}

	/**
	 * Gets the http method as lowercase string from the specified http method. See class constants for possible $method
	 * values.
	 *
	 * @param int $method
	 * @return string
	 * @throws \Yay\Core\Exception\ArgumentMismatchException
	 */
	public function getStringFromHttpMethod($method)
	{
		if ($method == self::MethodGet)
			return 'get';
		else if ($method == self::MethodPost)
			return 'post';
		else if ($method == self::MethodPut)
			return 'put';
		else if ($method == self::MethodDelete)
			return 'delete';
		else if ($method == self::MethodOptions)
			return 'options';
		else if ($method == self::MethodHead)
			return 'head';
		else if ($method == self::MethodTrace)
			return 'trace';
		else if ($method == self::MethodConnect)
			return 'connect';
		
		throw new Exception\ArgumentMismatchException("Invalid http method specified.");
	}
}