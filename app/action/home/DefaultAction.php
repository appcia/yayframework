<?php

namespace App\Action\Home;

use App\App;
use Yay\Core\Action\Action;

final class DefaultAction extends Action
{
	public function execute()
	{
		echo 'HOME ACTION!';
	}
}