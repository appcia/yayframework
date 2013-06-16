<?php

namespace App\Controller\Home;

use App\Action\Home\DefaultAction;
use Yay\Core\Controller\Controller;

final class HomeController extends Controller
{
	public function getHome($params)
	{
		return new DefaultAction($params);
	}
}