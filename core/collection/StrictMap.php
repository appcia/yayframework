<?php

namespace Yay\Core\Collection;

use Yay\Core\Exception\CollectionException;

class StrictMap extends Map
{
	/**
	 * @var string type for type checking
	 */
	private $_type;

	/**
	 * StrictMap constructor (uses the Map constructor).
	 *
	 * @param string $type item type (primitive or class name)
	 * @param null|array $data map data
	 * @param bool $readOnly is read-only?
	 */
	public function __construct($type, $data = null, $readOnly = false)
	{
		$this->_type = $type;
		parent::__construct($data, $readOnly);
	}

	/**
	 * Adds an item to the StrictMap. Overrides it's parent implementation (Map),
	 * checks the item's type.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return Map
	 * @throws \Yay\Core\Exception\CollectionException
	 */
	public function add($key, $value)
	{
		if ((is_object($value) && $value instanceof $this->_type) || gettype($value) == $this->_type)
			return parent::add($key, $value);

		throw new CollectionException("Can't add an item with type '" . gettype($value) . "' to a Map<" . $this->_type . ">.");
	}
}