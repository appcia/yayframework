<?php

namespace Yay\Core\View;

use Yay\Core\Response\HtmlResponse;
use Yay\Core\Response\JsonResponse;
use Yay\Core\Response\Response;
use Yay\Core\View\Template\iTemplate;
use Yay\Core\yComponent;

class View extends yComponent
{
	/**
	 * @var \Yay\Core\View\Template\iTemplate
	 */
	private $_template;
	/**
	 * @var \Yay\Core\Response\Response|\Yay\Core\Response\HtmlResponse|\Yay\Core\Response\JsonResponse
	 */
	private $_response;

	/**
	 * Construct.
	 *
	 * @param Response $response
	 * @param iTemplate $template
	 */
	public function __construct(Response $response, iTemplate $template = null)
	{
		$this->setResponse($response);
		if ($template)
			$this->setTemplate($template);
	}

	public function execute()
	{
		if ($this->template() && $this->_response instanceof HtmlResponse)
			$this->_response->setContent($this->template()->generate());

		$this->_response->send(true);
	}

	/**
	 * Sets the app's response object.
	 *
	 * @param \Yay\Core\Response\Response $response
	 */
	public function setResponse(Response $response)
	{
		$this->_response = $response;
	}

	/**
	 * Gets the app's resonse object.
	 *
	 * @return \Yay\Core\Response\Response
	 */
	public function response()
	{
		return $this->_response;
	}

	/**
	 * Sets the view's template instance.
	 *
	 * @param iTemplate $template
	 */
	public function setTemplate(iTemplate $template)
	{
		$this->_template = $template;
	}

	/**
	 * Gets the view's template instance.
	 *
	 * @return iTemplate
	 */
	public function template()
	{
		return $this->_template;
	}
}