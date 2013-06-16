<?php

namespace Yay\Core\Cache;

use Yay\Core\yComponent;
use Yay\Core\Exception;

class CacheManager extends yComponent
{
	/**
	 * Adds an iCache connection instance.
	 *
	 * @param string $name
	 * @param iCache $connection
	 */
	public function addConnection($name, iCache $connection)
	{
		$this->registerComponent('connection_' . $name, $connection);
	}

	/**
	 * Adds multiple connection instances.
	 *
	 * @param array $connections
	 */
	public function addConnections(array $connections)
	{
		foreach ($connections as $name => $connection)
			$this->addConnection($name, $connection);
	}

	/**
	 * Gets an array containing iCache instances from the specified config array.
	 * Every connection in the config array must follow this format with the same order:
	 * 'type' => 'xyz', classname
	 * 'constructorArgumentName' => 'value',
	 * 'constructorArgument2Name' => 'value',
	 * ....
	 *
	 * @param array $config
	 * @return array
	 */
	public function getConnectionsFromConfigArray(array $config)
	{
		$connections = array();
		foreach ($config as $connectionName => $properties)
		{
			$arguments = (array)$properties;
			unset($arguments['type']);
			$reflector = new \ReflectionClass($properties->type);
			$instance = $reflector->newInstanceArgs($arguments);
			$connections[$connectionName] = $instance;
		}

		return $connections;
	}

	/**
	 * Gets a connection instance with the specified name. If no name present, returns the
	 * 'default' connection.
	 *
	 * @param string $name
	 */
	public function get($name = 'default')
	{
		try
		{
			if (!$name)
				$name = 'default';

			return $this->component('connection_' . $name);
		}
		catch (Exception\ComponentNotFoundException $e)
		{
			throw new Exception\CacheException("Connection {$name} not found.");
		}
	}

	/**
	 * Calls disconnect() on all connection instances.
	 */
	public function disconnectAll()
	{
		foreach ($this->getAllConnections() as $connection)
			$connection->disconnect();
	}

	/**
	 * Gets all connection instances.
	 *
	 * @return array
	 */
	public function getAllConnections()
	{
		$components = $this->registeredComponents();
		$connections = array();
		foreach ($components as $name => $component)
		{
			if ($component->instance instanceof iCache)
				$connections[str_replace('connection_', '', $name)] = $component->instance;
		}

		return $connections;
	}
}