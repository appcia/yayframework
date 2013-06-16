<?php

namespace Yay\Core\Database\Connection\SQLite3;

use Yay\Core\Database\Connection\CacheableDatabaseConnection;
use Yay\Core\Exception;

/**
 * SQLite3 connection class. It uses PDO.
 *
 * @author BlindingLight<bloodredshade@gmail.com>
 * @package Yay\Core\Database\Connection\SQLite3
 */
class SQLite3Connection extends CacheableDatabaseConnection
{
	/**
	 * @var \PDO
	 */
	private $_pdo;
	/**
	 * @var string
	 */
	private $_connectionString;
	/**
	 * @var bool
	 */
	private $_isPersistent;

	/**
	 * Construct.
	 *
	 * @param string $name connection name
	 * @param string $dsn SQLite file path or :memory: for in-memory database
	 * @param bool $persistent persistent connection?
	 */
	public function __construct($name, $dsn = ':memory:', $persistent = false)
	{
		// creating database file if not exists
		if ($dsn != ':memory:' && !file_exists($dsn))
			@touch($dsn);

		$this->_connectionString = 'sqlite:' . $dsn;
		$this->_isPersistent = (bool)$persistent;
		$this->setName($name);
	}

	/**
	 * Connects to the database.
	 *
	 * @return bool
	 */
	public function connect()
	{
		try
		{
			$this->_pdo = new \PDO(
				$this->_connectionString,
				null,
				null,
				array(
					\PDO::ATTR_PERSISTENT => $this->_isPersistent
				)
			);

			$this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return true;
		}
		catch (\Exception $e)
		{
			$this->_lastErrorMessage = $e->getMessage();
			return false;
		}
	}

	/**
	 * Disconnects from the database.
	 */
	public function disconnect()
	{
		$this->_pdo = null;
	}

	/**
	 * Returns whether the database connection is alive.
	 *
	 * @return bool
	 */
	public function connected()
	{
		return $this->_pdo instanceof \PDO;
	}

	/**
	 * Performs a prepared query.
	 *
	 * @param string $query
	 * @param array $args
	 * @return array|int
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function query($query, array $args)
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't do query on a closed connection.");
	}

	/**
	 * Performs a prepared query, and returns one row. If there are more than one row, or no row
	 * returned, throws a DatabaseQueryException.
	 *
	 * @param string $query
	 * @param array $args
	 * @return object
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function queryOne($query, array $args)
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't do queryOne on a closed connection.");
	}

	/**
	 * Performs a "raw" query. Usable for queries that don't need parameters.
	 *
	 * @param string $query
	 * @return array|int
	 * @throws \Yay\Core\Exception\DatabaseException
	 * @throws \Yay\Core\Exception\DatabaseQueryException
	 */
	public function rawQuery($query)
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't do rawQuery on a closed connection.");

		try
		{
			$statement = $this->_pdo->query($query);
			$statement->closeCursor();
		}
		catch (\PDOException $e)
		{
			throw new Exception\DatabaseQueryException("Query error: " . $e->getMessage());
		}

		if ($statement === false)
			throw new Exception\DatabaseQueryException("Query error: " . $this->getLastError());

		return $statement->fetchAll();
	}

	/**
	 * Performs a "raw" query. Usable for queries that don't need parameters. If there are more
	 * than one row, or no row returned, throws a DatabaseQueryException.
	 *
	 * @param string $query
	 * @return array|int
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function rawQueryOne($query)
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't do rawQueryOne on a closed connection.");
	}

	/**
	 * Gets the last error message. If \PDO::errorCode() not equals to 0, returns the error code.
	 *
	 * @return string
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function getLastError()
	{
		if ($this->_pdo instanceof \PDO && $this->_pdo->errorCode())
			return $this->_pdo->errorCode();

		return parent::getLastError();
	}

	/**
	 * Begins a transaction.
	 *
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function begin()
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't start transaction on a closed connection.");
	}

	/**
	 * Performs commit (end of transaction).
	 *
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function commit()
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't do commit on a closed connection.");
	}

	/**
	 * Performs a rollback. If $savepoint specified, rollbacks to that.
	 *
	 * @param null|string $savepoint
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function rollback($savepoint = null)
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't do rollback transaction on a closed connection.");
	}

	/**
	 * Creates a savepoint.
	 *
	 * @param string $savepoint
	 * @throws \Yay\Core\Exception\DatabaseException
	 */
	public function savepoint($savepoint)
	{
		if (!$this->connected())
			throw new Exception\DatabaseException("Can't create a savepoint on a closed connection.");
	}


}