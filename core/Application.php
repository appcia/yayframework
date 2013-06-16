<?php

namespace Yay\Core;

use \Yay\Core\Exception;

/**
 * Base class for your application.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core
 */
abstract class Application extends yComponent
{
	/**
	 * @var Application last instance created
	 */
	private static $_instance;

	/**
	 * Construct. DON'T overwrite it, or use parent::__construct() in order to make ::instance()
	 * working.
	 */
	public function __construct()
	{
		self::$_instance = $this;
	}

	/**
	 * Returns the last created instance of this class. Ideal for accessing components statically.
	 * Application is not designed to be a singleton, but try to use one instance of it if you
	 * want to access it's component's statically.
	 *
	 * @return Application
	 */
	public static function instance()
	{
		return self::$_instance;
	}

	/**
	 * Gets a component.
	 *
	 * @param string $name
	 * @return callable|yComponent
	 */
	public function __get($name)
	{
		return $this->component($name);
	}

	/**
	 * Sets a component.
	 *
	 * @param string $name
	 * @param callable|yComponent $value
	 */
	public function __set($name, $value)
	{
		$this->registerComponent($name, $value);
	}

	/**
	 * Gets a component. You may add methods to access your components for auto-completing in IDEs.
	 *
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 * @throws Exception\ComponentNotCallableException
	 */
	public function __call($name, $args)
	{
		$component = $this->component($name);
		if (is_callable($component))
			return call_user_func_array($component, $args);
		else
			throw new Exception\ComponentNotCallableException("Component $name (" . gettype($component) . ") is not callable.");
	}

	/**
	 * Gets a component statically. You may add methods to access your components for auto-completing in IDEs.
	 *
	 * @param string $name
	 * @param array $args
	 * @return mixed|yComponent
	 * @throws \RuntimeException
	 * @throws Exception\ComponentNotCallableException
	 */
	public static function __callStatic($name, $args)
	{
		if (self::instance())
		{
			$component = self::instance()->component($name);
			if ($component instanceof yComponent)
				return $component;
			else if (is_callable($component))
				return call_user_func_array($component, $args);
			else
				throw new Exception\ComponentNotCallableException("Component $name (" . gettype($component) . ") is not callable or not an instance of yComponent.");
		}

		throw new \RuntimeException("Can't get application instance.");
	}

	/**
	 * Gets an iDatabaseConnection instance.
	 *
	 * @param string|null $connectionName
	 * @return \Yay\Core\Database\Connection\iDatabaseConnection
	 * @throws \RuntimeException
	 */
	public static function database($connectionName = null)
	{
		try
		{
			return self::instance()->component('database')->get($connectionName);
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get connection '$connectionName', missing DatabaseManager instance.");
		}
	}

	/**
	 * Gets the app's Request instance.
	 *
	 * @return \Yay\Core\Request\Request
	 * @throws \RuntimeException
	 */
	public static function request()
	{
		try
		{
			return self::instance()->component('request');
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get Request instance.");
		}
	}

	/**
	 * Gets the app's Router instance.
	 *
	 * @return \Yay\Core\Routing\Router
	 * @throws \RuntimeException
	 */
	public static function router()
	{
		try
		{
			return self::instance()->component('router');
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get Router instance.");
		}
	}

	/**
	 * Gets the app's SessionManager instance.
	 *
	 * @return \Yay\Core\Session\SessionManager
	 * @throws \RuntimeException
	 */
	public static function session()
	{
		try
		{
			return self::instance()->component('session');
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get SessionManager instance.");
		}
	}

	/**
	 * Gets the app's iFileSystem instance.
	 *
	 * @return \Yay\Core\FileSystem\iFileSystem
	 * @throws \RuntimeException
	 */
	public static function fileSystem()
	{
		try
		{
			return self::instance()->component('fileSystem');
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get iFileSystem instance.");
		}
	}

	/**
	 * Gets the app's Config instance.
	 *
	 * @return \Yay\Core\Config\Config
	 * @throws \RuntimeException
	 */
	public static function config()
	{
		try
		{
			return self::instance()->component('config');
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get Config instance.");
		}
	}

	/**
	 * Gets the app's CacheManager instance.
	 *
	 * @return \Yay\Core\Cache\CacheManager
	 * @throws \RuntimeException
	 */
	public static function cache()
	{
		try
		{
			return self::instance()->component('cache');
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new \RuntimeException("Can't get CacheManager instance.");
		}
	}

	abstract public function run();
}