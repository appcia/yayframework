<?php

namespace Yay\Core\Request;

use Yay\Core\FileSystem\iFileSystem;
use Yay\Core\Session\Storage\iSessionStorage;
use Yay\Core\yComponent;
use Yay\Core\Exception;

class Input extends yComponent
{
	/**
	 * @var array array containing input values of previous request
	 */
	private $_oldInput;

	/**
	 * Construct.
	 *
	 * @param iSessionStorage $session iSessionStorage instance for keeping input items
	 * @param iFileSystem $fileSystem iFileSystem instance for handling file uploads
	 */
	public function __construct(iSessionStorage $session, iFileSystem $fileSystem)
	{
		$this->registerComponent('session', $session);
		$this->registerComponent('fileSystem', $fileSystem);
		$old = $this->session()->get('input_keep_storage', false);
		$this->_oldInput = $old ? $old : array();
	}

	/**
	 * Gets an array containing all uploaded files. Each item is a $fileClassName class instance.
	 *
	 * @param string $fileClassName
	 * @return array
	 */
	public function files($fileClassName = '\Yay\Core\FileSystem\UploadedFile')
	{
		$files = array();
		foreach ($_FILES as $name => $file)
			$files[$name] = new $fileClassName($this->fileSystem(), $file['tmpName'], $file['name'], $file['error']);

		return $files;
	}

	/**
	 * Gets an uploaded file as a $fileClassName class instance.
	 *
	 * @param string $fileName
	 * @param string $fileClassName
	 * @return \Yay\Core\FileSystem\UploadedFile
	 * @throws \Yay\Core\Exception\FileNotFoundException
	 */
	public function file($fileName, $fileClassName = '\Yay\Core\FileSystem\UploadedFile')
	{
		if (!isset($_FILES[$fileName]))
			throw new Exception\FileNotFoundException("Can't get uploaded file $fileName.");

		$file = $_FILES[$fileName];
		return new $fileClassName($this->fileSystem(), $file['tmpName'], $file['name'], $file['error']);
	}

	/**
	 * Creates a $fileClassName class instance from the php input. It's usually used to handle
	 * ajax file uploads.
	 *
	 * @param string $fileClassName
	 * @return \Yay\Core\FileSystem\UploadedFile
	 * @throws \RuntimeException
	 */
	public function fileFromInput($fileClassName = '\Yay\Core\FileSystem\UploadedFile')
	{
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if (!isset($_SERVER['CONTENT_LENGTH']))
			throw new \RuntimeException("Can't get content length.");

		if ($realSize != (int)$_SERVER['CONTENT_LENGTH'])
			throw new \RuntimeException('Invalid file size.');

		$fileInfo = stream_get_meta_data($temp);
		return new $fileClassName($this->fileSystem(), realpath($fileInfo['uri']), basename($fileInfo['uri']), 0);
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
	 * Gets the iSessionStorage instance.
	 *
	 * @return \Yay\Core\Session\Storage\iSessionStorage
	 */
	protected function session()
	{
		return $this->component('session');
	}

	/**
	 * Gets an item from the previous request's input.
	 *
	 * @param string $name
	 * @param null|mixed $default
	 * @return null|mixed
	 */
	public function old($name, $default = null)
	{
		return isset($this->_oldInput[$name]) ? $this->_oldInput[$name] : $default;
	}

	/**
	 * Keep all input items for the next request.
	 */
	public function keep()
	{
		$this->session()->set('input_keep_storage', $this->all());
	}

	/**
	 * Keeps only the items with the specified key(s) for the next request.
	 *
	 * @param string
	 */
	public function keepOnly()
	{
		$this->session()->set(
			'input_keep_storage',
			call_user_func_array(
				array($this, 'only'),
				func_get_args()
			)
		);
	}

	/**
	 * Keeps all items except the specified ones for the next request.
	 *
	 * @param mixed
	 * @return array
	 */
	public function keepExcept()
	{
		$this->session()->set(
			'input_keep_storage',
			call_user_func_array(
				array($this, 'except'),
				func_get_args()
			)
		);
	}

	/**
	 * Gets all elements from the input.
	 *
	 * @return mixed
	 */
	public function all()
	{
		return $_REQUEST;
	}

	/**
	 * Returns the items with the specified key(s).
	 *
	 * @param mixed
	 * @return array
	 */
	public function only()
	{
		$items = array();
		$args = func_get_args();
		foreach ($args as $key)
			$items[$key] = $this->get($key);

		return $items;
	}

	/**
	 * Returns all items except the specified ones.
	 *
	 * @param mixed
	 * @return array
	 */
	public function except()
	{
		$items = $this->all();
		$args = func_get_args();
		foreach ($args as $key)
		{
			if (isset($items[$key]) || array_key_exists($key, $items))
				unset($items[$key]);
		}

		return $items;
	}

	/**
	 * Returns whether the input format is JSON.
	 *
	 * @return bool
	 */
	public function isJson()
	{
		return is_object(json_decode(file_get_contents('php://input')));
	}

	/**
	 * Returns the json input as an object.
	 *
	 * @return object
	 */
	public function getJson()
	{
		return json_decode(file_get_contents('php://input'));
	}

	/**
	 * Returns all input items in json format.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return json_encode($_REQUEST);
	}

	/**
	 * Returns whether the input has an item with the specified key.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function exists($name)
	{
		return isset($_REQUEST[$name]) || array_key_exists($name, $_REQUEST);
	}

	/**
	 * Gets an item from the input.
	 *
	 * @param string $name
	 * @param null|string $default
	 * @return null|mixed
	 */
	public function get($name, $default = null)
	{
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
	}

	/**
	 * Gets an item from the input as string.
	 *
	 * @param string $name
	 * @param null|string $default
	 * @return string
	 */
	public function getString($name, $default = null)
	{
		return (string)$this->get($name, $default);
	}

	/**
	 * Gets an item from the input as int.
	 *
	 * @param string $name
	 * @param null|string $default
	 * @return int
	 */
	public function getInt($name, $default = null)
	{
		return (int)$this->get($name, $default);
	}

	/**
	 * Gets an item from the input as float.
	 *
	 * @param string $name
	 * @param null|string $default
	 * @return float
	 */
	public function getFloat($name, $default = null)
	{
		return (float)$this->get($name, $default);
	}

	/**
	 * Gets an item from the input as double.
	 *
	 * @param string $name
	 * @param null|string $default
	 * @return float
	 */
	public function getDouble($name, $default = null)
	{
		return (double)$this->get($name, $default);
	}

	/**
	 * Gets an item from the input as bool.
	 *
	 * @param string $name
	 * @param null|string $default
	 * @return bool
	 */
	public function getBool($name, $default = null)
	{
		$value = $this->get($name, $default);
		return $value === true || $value == 'true' ? true : false;
	}
}