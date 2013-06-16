<?php

// if auto-creating connection instances from this config,
// the connection name (array key) will always be passed as first argument
return array(
	'default' => (object)array(
		'type' => '\Yay\Core\Database\Connection\PostgreSQL\PgSqlConnection',
		'host' => '127.0.0.1',
		'port' => 5432,
		'user' => 'postgres',
		'password' => '',
		'database' => 'test',
		'persistent' => false,
		'lazyConnect' => true
	)
);