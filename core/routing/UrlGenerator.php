<?php

namespace Yay\Core\Routing;

use Yay\Core\Request\Request;
use Yay\Core\yComponent;

class UrlGenerator extends yComponent
{
	private $_routes = array();

	/**
	 * Construct.
	 *
	 * @param array $routes
	 * @param Request $request current request
	 */
	public function __construct(array $routes, Request $request)
	{
		foreach ($routes as $mappedUrl => $route)
		{
			if (isset($route['as']))
			{
				if (isset($route['sameAs']) && isset($this->_routes[$route['sameAs']]))
					$this->addRoute($route['as'], $this->_routes[$route['sameAs']]);
				else
					$this->addRoute($route['as'], $mappedUrl);
			}
			else
				$this->addRoute($mappedUrl, $mappedUrl);
		}

		$this->registerComponent('request', $request);
	}

	/**
	 * Adds a route. If $mappedUrl is empty, $name will be used as $mappedUrl.
	 *
	 * @param string $name
	 * @param string $mappedUrl
	 */
	public function addRoute($name, $mappedUrl = null)
	{
		$this->_routes[$name] = $mappedUrl ? $mappedUrl : $name;
	}

	/**
	 * Removes a route.
	 *
	 * @param string $name
	 */
	public function removeRoute($name)
	{
		if (isset($this->_routes[$name]))
			unset($this->_routes[$name]);
	}

	/**
	 * Gets properties from string. Format: name:value|name2:value2
	 *
	 * @param string $properties
	 * @return array
	 */
	public function getPropertiesFromString($properties = '')
	{
		// id:test|name:nobody
		$tmp = explode('|', $properties);
		$prepared = array();
		foreach ($tmp as $property)
		{
			$ptemp = explode(':', $property);
			if (count($ptemp))
				$prepared[$ptemp[0]] = isset($ptemp[1]) ? $ptemp[1] : '';
		}

		return $prepared;
	}

	/**
	 * Generates url from the specified mapped url. $properties can be an array or a string. If it's an array,
	 * it should be array('placeholderName' => 'value', ...). If string, it should be
	 * 'placeholderName:value|placeholder2Name:value@domain@secure'. @domain and @secure are optional. String $properties
	 * is about 2x slower than array $properties.
	 * Example:
	 * mappedUrl('user/{id|[0-9]+}', array('id' => 1), true, true) -> https://domain.com/user/1
	 * mappedUrl('user/{id|[0-9]+}', 'id:1@domain@secure') -> https://domain.com/user/1
	 *
	 * @param string $mappedUrl
	 * @param string $properties
	 * @param bool $withDomain
	 * @param bool $secure
	 * @return string
	 */
	public function mappedUrl($mappedUrl, $properties = '', $withDomain = false, $secure = false)
	{
		return $this->route($mappedUrl, $properties, $withDomain, $secure);
	}


	/**
	 * Generates url from the specified route's mapped url. If route doesn't exist, it uses $name as $mappedUrl.
	 * $properties can be an array or a string. If it's an array, it should be
	 * array('placeholderName' => 'value', ...). If string, it should be
	 * 'placeholderName:value|placeholder2Name:value@domain@secure'. @domain and @secure are optional. String $properties
	 * is about 2x slower than array $properties.
	 * Example:
	 * mappedUrl('user/{id|[0-9]+}', array('id' => 1), true, true) -> https://domain.com/user/1
	 * mappedUrl('user/{id|[0-9]+}', 'id:1@domain@secure') -> https://domain.com/user/1
	 *
	 * @param string $name
	 * @param string $properties
	 * @param bool $withDomain
	 * @param bool $secure
	 * @return string
	 */
	public function route($name, $properties = '', $withDomain = false, $secure = false)
	{
		$preparedUrl = isset($this->_routes[$name]) ? $this->prepareRouteMappedUrl($this->_routes[$name]) : $name;

		if (is_string($properties))
		{
			if (strpos($properties, '@domain') !== -1)
			{
				$withDomain = true;
				$properties = str_replace('@domain', '', $properties);
			}
			if (strpos($properties, '@secure') !== -1)
			{
				$secure = true;
				$properties = str_replace('@secure', '', $properties);
			}
		}
		$properties = is_array($properties) ? $properties : $this->getPropertiesFromString($properties);

		foreach ($properties as $name => $value)
		$preparedUrl = str_replace('{' . $name . '}', $value, $preparedUrl);

		return ($withDomain ? ($secure ? 'https://' : 'http://') . $this->request()->domainName() . '/' : '') . $preparedUrl;
	}

	/**
	 * Prepares a mapped url. (changes {name|regexp} to {name})
	 *
	 * @param $mappedUrl
	 * @return mixed
	 */
	public function prepareRouteMappedUrl($mappedUrl)
	{
		return preg_replace('#\{(\w+)\|[^\}]+}#', '{$1}', $mappedUrl);
	}

	/**
	 * Gets the Request instance.
	 *
	 * @return Request
	 */
	protected function request()
	{
		return $this->component('request');
	}
}