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
	public function addExternalClasses($externalClasses)
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
		return isset($this->_externalClasses[$className])
			? 'external/' . $this->_externalClasses[$className]
			: null;
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