<?php

namespace Yay\Core\Routing;

use Yay\Core\Request\Request;
use Yay\Core\yComponent;
use Yay\Core\Exception;

/**
 * Routes the request uri.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Routing
 */
class Router extends yComponent
{
	/**
	 * @var array
	 */
	private $_routes = array();
	/**
	 * @var array url -> route mappings
	 */
	private $_urlMappings = array();
	/**
	 * @var string
	 */
	private $_defaultRoute = '/';
	/**
	 * @var string
	 */
	private $_matchedMappedUrl;

	/**
	 * Construct.
	 *
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->registerComponent('request', $request);
	}

	/**
	 * Sets the default route. This will be used if no match found.
	 *
	 * @param string $routeName
	 */
	public function setDefaultRoute($routeName)
	{
		$this->_defaultRoute = $routeName;
	}

	/**
	 * Gets the default route name.
	 *
	 * @return string
	 */
	public function defaultRouteName()
	{
		return $this->_defaultRoute;
	}

	/**
	 * Routes the request uri. And returns the executed route function/method result.
	 *
	 * @return \Yay\Core\Action\Action|mixed
	 * @throws \Yay\Core\Exception\ConfigException
	 */
	public function routeUri()
	{
		$route = $this->matchUri($this->request()->uri());
		$route->parameters = $this->getParameters($this->_matchedMappedUrl, $route->parameters);
		$route->method = $this->request()->httpMethodString();

		if (isset($route->{$route->method}))
			$route->method = 'get';

		if (!isset($route->{$route->method}))
			throw new Exception\ConfigException("Badly configured route, missing 'get'.");

		if (is_callable($route->{$route->method}))
			return call_user_func_array($route->{$route->method}, $route->parameters);
		else
		{
			list($className, $methodName) = explode('@', $route->{$route->method});
			$controller = new $className();
			return $controller->{$methodName}($route->parameters);
		}
	}

	/**
	 * Creates an associative parameters array for the given mapped url from the $parameters array.
	 *
	 * @param string $mappedUrl
	 * @param array $parameters an array containing parameter values, must be number indexed
	 * @return array
	 */
	protected function getParameters($mappedUrl, $parameters)
	{
		if (!$mappedUrl || $mappedUrl == '/')
			return array();

		$parts = explode('/', $mappedUrl);
		$finalized = array();
		foreach ($parts as $part)
		{
			if (preg_match('/{[a-zA-Z0-9-_]+\|.+}/', $part))
				$finalized[preg_replace('/{([a-zA-Z0-9]+)\|(.+)}/', '$1', $part)] = array_shift($parameters);
		}

		return $finalized;
	}

	/**
	 * Matches the request uri and returns an object with route data.
	 *
	 * @param string $uri
	 * @return object
	 */
	protected function matchUri($uri)
	{
		$uri = $uri != '/' ? trim($uri, '/') : $uri;
		foreach ($this->_urlMappings as $mappedUrl => $routeName)
		{
			$pattern = $this->createRegexFromMappedUrl($mappedUrl);
			if (!$pattern)
				continue;

			$matches = array();
			if (preg_match($pattern, $uri, $matches))
			{
				$routeName = $this->_urlMappings[$mappedUrl];
				if (isset($this->_routes[$routeName]->domain)
					&& !$this->domainCheck($this->request()->domainName(), $this->_routes[$routeName]->domain))
				{
					continue;
				}

				$this->_routes[$routeName]->parameters = array_splice($matches, 1, count($matches) - 1);
				$this->_matchedMappedUrl = $mappedUrl;
				return $this->_routes[$routeName];
			}
		}

		$route = $this->_routes[$this->defaultRouteName()];
		$route->parameters = array();
		return $route;
	}

	/**
	 * Checks whether the current request domain is valid for the route.
	 *
	 * @param string $domain current domain
	 * @param string $expected expected domain (route domain)
	 * @return bool
	 */
	protected function domainCheck($domain, $expected)
	{
		$domainParts = explode('.', $domain);
		$expectedParts = explode('.', $expected);

		if (count($domainParts) != count($expectedParts))
			return false;

		foreach ($domainParts as $idx => $domainPart)
		{
			$expectedPart = $expectedParts[$idx];
			if ($expectedPart != '*' && $domainPart != $expectedPart)
				return false;
		}

		return true;
	}

	/**
	 * Creates a regex pattern from a mapped url. Used for matching url -> mapped url matching.
	 *
	 * @param string $mappedUrl
	 * @return string
	 */
	protected function createRegexFromMappedUrl($mappedUrl)
	{
		if (!$mappedUrl || $mappedUrl == '/')
			return '#/#';

		$regex = '/^';
		$parts = explode('/', $mappedUrl);
		foreach ($parts as $idx => $part)
		{
			$regex .= !$idx ? '' : '\/';
			if (preg_match('/{[a-zA-Z0-9]+\|.+}/', $part))
				$regex .= '(' . preg_replace('/{([a-zA-Z0-9]+)\|(.+)}/', '$2', $part) . ')';
			else
				$regex .= $part;
		}

		return $regex . '$/';
	}

	/**
	 * Adds a route. Route must be a callable function or a string with format 'className[at]methodName'.
	 *
	 * @param string $mappedUrl
	 * @param \Callable|string $route
	 * @throws \Yay\Core\Exception\ConfigException
	 */
	public function addRoute($mappedUrl, $route)
	{
		if (isset($this->_routes[$mappedUrl]))
			throw new Exception\ConfigException("Can't add route '$mappedUrl', already exists.");

		$route = (object)$route;
		$routeName = isset($route->as) ? $route->as : $mappedUrl;
		$this->_urlMappings[$mappedUrl] = $routeName;

		if (isset($route->sameAs) && isset($this->_routes[$route->sameAs]))
		{
			$this->_routes[$routeName] = $this->_routes[$route->sameAs];
			return;
		}

		$this->_routes[$routeName] = $route;
	}

	/**
	 * Adds multiple routes.
	 *
	 * @param array $routes
	 */
	public function addRoutes(array $routes)
	{
		foreach ($routes as $url => $route)
			$this->addRoute($url, $route);
	}

	/**
	 * Gets Request instance.
	 *
	 * @return \Yay\Core\Request\Request
	 */
	protected function request()
	{
		return $this->component('request');
	}
}