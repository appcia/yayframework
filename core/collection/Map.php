<?php

namespace Yay\Core\Collection;

use Yay\Core\Exception\CollectionException;
use Yay\Core\yComponent;

/**
 * A basic map.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Collection
 */
class Map extends yComponent implements \IteratorAggregate, \Countable, \ArrayAccess
{
	protected static $_componentVersion = 1.0;

	/**
	 * @var bool map is read-only?
	 */
	private $_readOnly = false;
	/**
	 * @var array map data
	 */
	private $_data;

	/**
	 * Map constructor. Fills the map with an array or \Traversable object data.
	 * @param null|\Traversable|array $data
	 * @param bool $readOnly
	 */
	public function __construct($data = null, $readOnly = false)
	{
		if ($data instanceof \Traversable || is_array($data))
			$this->copyFrom($data);

		$this->setReadOnly($readOnly);
	}

	/**
	 * Adds an item into the map. If the item already exists, it'll be overwritten.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return \Yay\Core\Collection\Map
	 * @throws \Yay\Core\Exception\CollectionException
	 */
	public function add($key, $value)
	{
		if ($this->_readOnly)
			throw new CollectionException("Can't add item to a read-only map.");

		$this->_data[$key] = $value;

		return $this;
	}

	/**
	 * Removes the item with the specified key.
	 *
	 * @param mixed $key
	 * @return \Yay\Core\Collection\Map
	 * @throws \Yay\Core\Exception\CollectionException
	 */
	public function remove($key)
	{
		if ($this->_readOnly)
			throw new CollectionException("Can't remove item from a read-only map.");

		if (isset($this->_data[$key]) || array_key_exists($key, $this->_data))
			unset($this->_data[$key]);

		return $this;
	}

	/**
	 * Returns the item with the specified key, or null if the item doesn't exist.
	 *
	 * @param mixed $key
	 * @return null|mixed
	 */
	public function get($key)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : null;
	}

	/**
	 * Clears the map.
	 *
	 * @return \Yay\Core\Collection\Map
	 * @throws \Yay\Core\Exception\CollectionException
	 */
	public function clear()
	{
		if ($this->_readOnly)
			throw new CollectionException("Can't clear a read-only map.");

		$this->_data = array();

		return $this;
	}

	/**
	 * Returns whether the map contains an item with the specified key.
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function contains($key)
	{
		return isset($this->_data[$key]) || array_key_exists($key, $this->_data);
	}

	/**
	 * Gets the map data as array.
	 *
	 * @return mixed
	 */
	public function toArray()
	{
		return $this->_data;
	}

	/**
	 * Copies the source data into the map. Existing data will be cleared first.
	 *
	 * @param $data
	 * @return \Yay\Core\Collection\Map
	 * @throws \Yay\Core\Exception\CollectionException
	 */
	public function copyFrom($data)
	{
		if ($data instanceof \Traversable || is_array($data))
		{
			if ($this->count() > 0)
				$this->clear();
			if (method_exists($data, 'toArray'))
				$data = $data->toArray();

			// must $this->add for StrictMap check
			foreach($data as $key => $value)
				$this->add($key, $value);
		}
		else if (!is_null($data))
			throw new CollectionException("Can't copy data to map, source must be a \\Traversable object or an array.");

		return $this;
	}

	/**
	 * Merges the map data with an array or \Traversable object data using array_merge.
	 *
	 * @param $data
	 * @return \Yay\Core\Collection\Map
	 * @throws \Yay\Core\Exception\CollectionException
	 */
	public function mergeWith($data)
	{
		if (!is_array($data) && !($data instanceof \Traversable))
			throw new CollectionException("Can't merge with data, source must be a \\Traversable object or an array.");
		$tmp = is_array($data) ? $data : array();
		if (!is_array($data))
		{
			foreach ($data as $key => $value)
				$tmp[$key] = $value;
		}

		$this->_data = array_merge($this->_data, $data);

		return $this;
	}

	/**
	 * Sets that the map is read only or not.
	 *
	 * @param bool $readOnly
	 * @return \Yay\Core\Collection\Map
	 */
	public function setReadOnly($readOnly)
	{
		$this->_readOnly = !(!$readOnly);
		return $this;
	}

	/**
	 * Returns that the map is read only or not.
	 *
	 * @return bool
	 */
	public function readOnly()
	{
		return $this->_readOnly;
	}

	/**
	 * Returns an iterator for traversing the items in the map.
	 *
	 * @return \Traversable|MapIterator MapIterator instance
	 */
	public function getIterator()
	{
		return new MapIterator($this->_data);
	}

	/**
	 * Returns the number of items in the map.
	 *
	 * @return int number of items
	 */
	public function count()
	{
		return count($this->_data);
	}

	/**
	 * Returns whether there is an element at the specified offset.
	 *
	 * @param mixed $offset the offset to check on
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->contains($offset);
	}

	/**
	 * Returns the element at the specified offset.
	 *
	 * @param integer $offset the offset to retrieve element.
	 * @return mixed the element at the offset, null if no element is found at the offset
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * Sets the element at the specified offset.
	 *
	 * @param integer $offset the offset to set element
	 * @param mixed $item the element value
	 */
	public function offsetSet($offset, $item)
	{
		$this->add($offset, $item);
	}

	/**
	 * Unsets the element at the specified offset.
	 *
	 * @param mixed $offset the offset to unset element
	 */
	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}
}