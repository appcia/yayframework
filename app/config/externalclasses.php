<?php

return array(
	'resolvers' => array(
		'Twig' => function($className)
		{
			return str_replace(array('_', "\0"), array('/', ''), $className) . '.php';
		}
	),
	'classes' => array(

	)
);