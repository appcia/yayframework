<?php

namespace Yay\Core;

use Yay\Core\Exception;

/**
 * Autoloader.
 *
 * @package Yay\Core
 * @author BlindingLight<bloodredshade@gmail.com>
 */
class Autoloader extends yComponent
{
	protected static $_componentVersion = 1.1;

	/**
	 * @var array External class mappings
	 */
	private $_externalClasses = array();
	/**
	 * @var array External class resolver callable functions
	 */
	private $_externalResolvers = array();
	/**
	 * @var string Root directory, where autoloader can find files
	 */
	private $_rootDirectory = '';

	/**
	 * Sets the root directory. (relative)
	 *
	 * @param string $rootDirectory
	 */
	public function setRootDirectory($rootDirectory)
	{
		$this->_rootDirectory = $rootDirectory . '/';
	}

	/**
	 * Adds external class mappings. Expects an array in format array('className' => 'filePath', ...)
	 *
	 * @param $externalClasses
	 * @throws Exception\ArgumentMismatchException
	 */
	public function addExternalClasses(array $externalClasses)
	{
		if (!is_array($externalClasses))
			throw new Exception\ArgumentMismatchException("Couldn't add external classes, argument isn't an array");

		$this->_externalClasses = array_merge($this->_externalClasses, $externalClasses);
	}

	/**
	 * Adds an external class mapping.
	 *
	 * @param $className
	 * @param $filePath
	 */
	public function addExternalClass($className, $filePath)
	{
		$this->_externalClasses[$className] = $filePath;
	}

	/**
	 * Adds an external class resolver.
	 *
	 * @param int $stringStart
	 * @param callable $callable
	 */
	public function addExternalResolver($stringStart, $callable)
	{
		$this->_externalResolvers[$stringStart] = $callable;
	}

	/**
	 * Adds external class resolvers. Expects an array in format array('className' => callable, ...)
	 *
	 * @param $resolvers
	 * @throws Exception\ArgumentMismatchException
	 */
	public function addExternalResolvers(array $resolvers)
	{
		if (!is_array($resolvers))
			throw new Exception\ArgumentMismatchException("Couldn't add external resolvers, argument isn't an array");

		$this->_externalResolvers = array_merge($this->_externalResolvers, $resolvers);
	}

	/**
	 * Autoloads a class.
	 *
	 * @param $className
	 * @throws Exception\AutoloadException
	 */
	public function autoload($className)
	{
		if (strpos($className, 'Yay') !== false)
			$filePath = $this->getInternalFileName($className);
		else
			$filePath = $this->getExternalFileName($className);

		$filePath = $this->_rootDirectory . ($filePath ? $filePath : $this->getPackageFileName($className));
		if (file_exists($filePath))
			require_once($filePath);
		else
			throw new Exception\AutoloadException("Couldn't find class $className ($filePath)");
	}

	/**
	 * Gets an internal class file name.
	 *
	 * @param $className
	 * @return string
	 */
	protected function getInternalFileName($className)
	{
		$tmp = explode('\\', $className);
		// remove Yay namespace
		array_shift($tmp);

		$namespace = '';
		$class = array_pop($tmp);

		foreach (array_values($tmp) as $namespaceName)
			$namespace .= $namespaceName . '\\';

		return strtolower($namespace) . $class . '.php';
	}

	/**
	 * Gets an external class file name.
	 *
	 * @param $className
	 * @return null|string
	 */
	protected function getExternalFileName($className)
	{
		if (isset($this->_externalClasses[$className]))
			return $this->_externalClasses[$className];

		foreach ($this->_externalResolvers as $startString => $callable)
		{
			if (strpos($className, $startString) === 0 && is_callable($callable))
			{
				$filePath = '/external/' . $callable($className);
				if (file_exists($this->_rootDirectory . $filePath))
					return $filePath;
			}
		}

		return null;
	}

	/**
	 * Gets a package class file name. This method will be used for app (that's a package too!!) classes or any other
	 * packages.
	 *
	 * @param $className
	 * @return mixed
	 */
	protected function getPackageFileName($className)
	{
		return str_replace('\\', '/', $className) . '.php';
	}
}