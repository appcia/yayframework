<?php

namespace App\Controller\Home;

use App\Action\Home\DefaultAction;
use App\App;
use Yay\Core\Controller\Controller;
use Yay\Core\Response\HtmlResponse;
use Yay\Core\View\Template\TwigTemplate;
use Yay\Core\View\View;

final class HomeController extends Controller
{
	public function getHome($params)
	{
		$home = new DefaultAction($params);
		$home->setView(
			new View(
				new HtmlResponse(),
				new TwigTemplate(
					App::config()->application->get('templates')->root,
					App::config()->application->get('templates')->compiled
				)
			)
		);
		$home->execute();
	}
}