<?php

require_once('../core/yComponent.php');
require_once('../core/Autoloader.php');

$autoloader = new \Yay\Core\Autoloader();
$autoloader->setRootDirectory('..');

spl_autoload_register(
	function($className) use($autoloader)
	{
		$autoloader->autoload($className);
	},
	true,
	true
);

// instantiating App
$app = new App\App();

// DON'T CHANGE THE ATTRIBUTE NAMES IF YOU WANT TO USE THE PREDEFINED ACCESSOR STATIC METHODS

// instantiating FileSystem
$app->fileSystem = new \Yay\Core\FileSystem\FileSystem();
// setting up config
$app->config = new \Yay\Core\Config\Config($app->fileSystem);
$app->config->getFilesInDirectory('../app/config');
// session config
$app->session = new \Yay\Core\Session\SessionManager(new \Yay\Core\Session\Storage\Native());
// request init
$app->request = new \Yay\Core\Request\Request();
// input init
$app->input = new \Yay\Core\Request\Input($app->session->storage(), $app->fileSystem);
// router init
$app->router = new \Yay\Core\Routing\Router($app->request);
$app->router->addRoutes($app->config->routes->toArray());
// cache connections
$app->cache = new \Yay\Core\Cache\CacheManager();
$app->cache->addConnections(
	$app->cache->getConnectionsFromConfigArray(
		$app->config->cacheconnections->toArray()
	)
);
// database connections
$app->database = new Yay\Core\Database\DatabaseManager();
$app->database->addConnections(
	$app->database->getConnectionsFromConfigArray(
		$app->config->databases->toArray()
	)
);

echo $app->request->host();
