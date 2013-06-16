<?php

namespace Yay\Core;

use Yay\Core\Exception;

/**
 * Class yComponent
 * Base class for ALL of the classes in yayframework. It provides basic component versioning
 * and registering.
 *
 * @package Yay\Core
 * @author BlindingLight<bloodredshade@gmail.com>
 */
abstract class yComponent
{
	protected static $_componentVersion = 1.0;
	private $_components = array();

	public function registerComponent($name, $component)
	{
		if (isset($this->_components[$name]))
			throw new Exception\ComponentAlreadyRegisteredException("Component $name already registered.");

		if (!($component instanceof yComponent) && !is_callable($component))
		{
			throw new Exception\ComponentInvalidException("Can't register invalid component $name ("
				. gettype($component) . "), must be inherited from yComponent or \\Callable.");
		}

		$this->_components[$name] = $component;
	}

	/**
	 * Gets a component.
	 *
	 * @param $name
	 * @return yComponent|\Closure
	 * @throws Exception\ComponentNotFoundException
	 */
	public function component($name)
	{
		if (!isset($this->_components[$name]))
			throw new Exception\ComponentNotFoundException("Component $name not found.");

		return $this->_components[$name];
	}

	/**
	 * Gets the registered components. (recursive)
	 * Item format:
	 * <ul>
	 * 		<li><b>name:</b> component name (alias)			<i>string</i></li>
	 * 		<li><b>isCallable:</b> component is callable?		<i>bool</i></li>
	 *		<li><b>type:</b> component type					<i>string</i></li>
	 * 		<li><b>class:</b> component's class				<i>string</i></li>
	 * 		<li><b>version:</b> component version				<i>int</i></li>
	 * 		<li><b>instance:</b> component instance				<i>int</i></li>
	 * 		<li><b>components:</b> components				<i>array</i></li>
	 * </ul>
	 *
	 * @return array
	 */
	public function registeredComponentsRecursive()
	{
		$components = array();
		foreach ($this->_components as $name => $component)
		{
			$components[$name] = (object)array(
				'name' => $name,
				'isCallable' => is_callable($component),
				'type' => gettype($component),
				'class' => is_object($component) ? get_class($component) : '',
				'version' => $component instanceof yComponent ? $component->getComponentVersion() : '',
				'instance' => $component,
				'components' => $component instanceof yComponent ? $component->registeredComponentsRecursive() : array()
			);
		}

		return $components;
	}

	/**
	 * Echoes the registered components in json format (json__encode). (recursive)
	 */
	public function registeredComponentsRecursiveJson()
	{
		echo json_encode($this->registeredComponentsRecursive());
	}

	/**
	 * Dumps the registered components with var_dump(). (recursive)
	 */
	public function dumpRegisteredComponentsRecursive()
	{
		var_dump($this->registeredComponentsRecursive());
	}

	/**
	 * Gets the registered components.
	 * Item format:
	 * <ul>
	 * 		<li><b>name:</b> component name (alias)			<i>string</i></li>
	 * 		<li><b>isCallable:</b> component is callable?		<i>bool</i></li>
	 *		<li><b>type:</b> component type					<i>string</i></li>
	 * 		<li><b>class:</b> component's class				<i>string</i></li>
	 * 		<li><b>version:</b> component version				<i>int</i></li>
	 * 		<li><b>instance:</b> component instance				<i>int</i></li>
	 * </ul>
	 *
	 * @return array
	 */
	public function registeredComponents()
	{
		$components = array();
		foreach ($this->_components as $name => $component)
		{
			$components[$name] = (object)array(
				'name' => $name,
				'isCallable' => is_callable($component),
				'type' => gettype($component),
				'class' => is_object($component) ? get_class($component) : '',
				'version' => $component instanceof yComponent ? $component->getComponentVersion() : '',
				'instance' => $component
			);
		}

		return $components;
	}

	/**
	 * Echoes the registered components in json format (json__encode).
	 */
	public function registeredComponentsJson()
	{
		echo json_encode($this->registeredComponents());
	}

	/**
	 * Dumps the registered components with var_dump().
	 */
	public function dumpRegisteredComponents()
	{
		var_dump($this->registeredComponents());
	}

	/**
	 * Gets the component's version.
	 *
	 * @return float
	 */
	public static function getComponentVersion()
	{
		return self::$_componentVersion;
	}
}