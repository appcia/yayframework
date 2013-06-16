<?php

namespace Yay\Core\Config;

use Yay\Core\FileSystem\iFileSystem;
use Yay\Core\yComponent;

/**
 * Helper class for managing configurations.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Config
 */
class Config extends yComponent
{
	/**
	 * Config constructor. You can set an iFileSystem instance.
	 *
	 * @param iFileSystem $fileSystem
	 */
	public function __construct(iFileSystem $fileSystem)
	{
		if ($fileSystem)
			$this->setFileSystem($fileSystem);
	}

	/**
	 * Adds a ConfigBag.
	 *
	 * @param $name
	 * @param ConfigBag $config
	 * @return \Yay\Core\Config\Config
	 */
	public function add($name, ConfigBag $config)
	{
		$this->registerComponent($name, $config);
		return $this;
	}

	/**
	 * Returns the ConfigBag instance with the specified name if Config called as a function.
	 * Example: $app->config('application')
	 *
	 * @param $name
	 * @return \Yay\Core\Config\ConfigBag
	 */
	public function __invoke($name)
	{
		return $this->component($name);
	}

	/**
	 * Returns the ConfigBag instance with the specified name if accessed like a property.
	 * Example: $app->config->application
	 *
	 * @param $name
	 * @return \Yay\Core\Config\ConfigBag
	 */
	public function __get($name)
	{
		return $this->component($name);
	}

	/**
	 * Gets a config array from a file, creates a ConfigBag instance with the name of the file
	 * (excluding extension) and returns the instance.
	 *
	 * @param string $filePath
	 * @return \Yay\Core\Config\ConfigBag
	 */
	public function getFile($filePath)
	{
		$bag = new ConfigBag($this->fileSystem());
		return $this->add(str_replace('.php', '', $this->fileSystem()->baseName($filePath)), $bag->getFile($filePath));
	}

	/**
	 * Gets config arrays from files in a directory and creates ConfigBag instance for each file.
	 * ConfigBag instance name will be the file name without extension.
	 * Files must match *.php pattern.
	 *
	 * @param $directory
	 * @return \Yay\Core\Config\ConfigBag
	 */
	public function getFilesInDirectory($directory)
	{
		$files = $this->fileSystem()->glob($directory . '/*.php');
		foreach ($files as $file)
			$this->getFile($file);

		return $this;
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
}