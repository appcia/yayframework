<?php

namespace Yay\Core\Session;

interface iSessionStorage
{
	/**
	 * Sets an item in session storage.
	 *
	 * @param $name
	 * @param $value
	 */
	function set($name, $value);

	/**
	 * Gets an item from session storage.
	 *
	 * @param string $name
	 * @return mixed
	 */
	function get($name);

	/**
	 * Removes an item from session storage.
	 *
	 * @param string $name
	 * @return bool
	 */
	function remove($name);

	/**
	 * Clears the session storage.
	 */
	function clear();

	/**
	 * Gets the storage data as an array.
	 *
	 * @return array
	 */
	function toArray();

	/**
	 * Ends the session.
	 *
	 * @return bool
	 */
	function end();
}