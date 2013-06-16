<?php

namespace Yay\Core\Config;

use Yay\Core\Collection\Map;
use Yay\Core\FileSystem\iFileSystem;

class ConfigBag extends Map
{
	/**
	 * Construct. Sets an iFileSystem class if specified.
	 *
	 * @param iFileSystem $fileSystem
	 */
	public function __construct(iFileSystem $fileSystem = null)
	{
		if ($fileSystem)
			$this->setFileSystem($fileSystem);
	}

	/**
	 * Gets a config property as string.
	 *
	 * @param string $name
	 * @return string
	 */
	public function getString($name)
	{
		return (string)$this->get($name);
	}

	/**
	 * Gets a config property as int.
	 *
	 * @param string $name
	 * @return int
	 */
	public function getInt($name)
	{
		return (int)$this->get($name);
	}

	/**
	 * Gets a config property as float.
	 *
	 * @param string $name
	 * @return float
	 */
	public function getFloat($name)
	{
		return (float)$this->get($name);
	}

	/**
	 * Gets a config property as double.
	 *
	 * @param string $name
	 * @return float
	 */
	public function getDouble($name)
	{
		return (double)$this->get($name);
	}

	/**
	 * Gets a config property as boolean.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function getBool($name)
	{
		if ($name == 'true')
			return true;
		if ($name == 'false')
			return false;

		return (bool)$this->get($name);
	}

	/**
	 * Gets a config property as an array.
	 *
	 * @param string $name
	 * @return array
	 */
	public function getArray($name)
	{
		return (array)$this->get($name);
	}

	/**
	 * Gets a config property as an object.
	 *
	 * @param string $name
	 * @return object
	 */
	public function getObject($name)
	{
		return (object)$this->get($name);
	}

	/**
	 * Sets the iFileSystem instance.
	 *
	 * @param iFileSystem $fileSystem
	 */
	public function setFileSystem(iFileSystem $fileSystem)
	{
		$this->registerComponent('fileSystem', $fileSystem);
	}

	/**
	 * Gets the iFileSystem instance.
	 *
	 * @return \Yay\Core\FileSystem\iFileSystem
	 */
	protected function fileSystem()
	{
		return $this->component('fileSystem');
	}

	/**
	 * Gets a config array from a file. Returns the ConfigBag instance.
	 *
	 * @param string $filePath
	 * @return \Yay\Core\Config\ConfigBag
	 */
	public function getFile($filePath)
	{
		return $this->copyFrom($this->fileSystem()->getRequire($filePath));
	}
}