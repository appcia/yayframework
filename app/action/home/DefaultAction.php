<?php

namespace App\Action\Home;

use App\App;
use Yay\Core\Action\Action;

final class DefaultAction extends Action
{
	public function execute()
	{
		$this->view()->template()->setTemplatePath('home/default/default.twig');
		$this->view()->execute();
	}
}