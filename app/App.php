<?php

namespace App;

use Yay\Core\Action\Action;
use Yay\Core\Application;

class App extends Application
{
	public function run()
	{
		self::router()->setDefaultRoute('404');
		$controllerResult = self::router()->routeUri();
		if ($controllerResult instanceof Action)
			$controllerResult->execute();
		else if (is_string($controllerResult))
			echo $controllerResult;
	}
}