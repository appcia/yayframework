<?php

namespace Yay\Core\Action;

use Yay\Core\yComponent;

/**
 * Base action class. You can use action classes for business logic.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Action
 */
abstract class Action extends yComponent
{
	private $_params = array();

	public function __construct(array $params)
	{
		$this->_params = $params;
	}

	private function get($name)
	{
		return isset($this->_params[$name]) ? $this->_params[$name] : null;
	}

	private function set($name, $value)
	{
		$this->_params[$name] = $value;
	}

	abstract public function execute();
}