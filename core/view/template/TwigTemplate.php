<?php

namespace Yay\Core\View\Template;

use Yay\Core\yComponent;

class TwigTemplate extends yComponent implements iTemplate
{
	private $_data = array();
	private $_twig;
	private $_templatePath = '';
	private $_templateRoot = '';

	public function __construct($templateRootDir, $compiledDir)
	{
		$this->_templateRoot = $templateRootDir . '/';
		$loader = new \Twig_Loader_Filesystem($templateRootDir);
		$this->_twig = new \Twig_Environment(
			$loader,
			array(
				'cache' => $compiledDir,
				'auto_reload' => true
			)
		);
	}

	public function assign($name, $value)
	{
		$this->_data[$name] = $value;
	}

	public function get($name, $default = null)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : $default;
	}

	public function generate()
	{
		if (!$this->_templatePath)
			$this->_templatePath = 'default.twig';

		return $this->_twig->render($this->_templatePath, $this->_data);
	}

	public function setTemplatePath($path)
	{
		$this->_templatePath = $path;
	}
}