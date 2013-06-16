<?php

return array(
	// default
	'/' => array(
		'get' => '\App\Controller\Home\HomeController@getHome'
	),
	'404' => array(
		'get' => function()
		{
			return 'Page doesn\'t exist.';
		}
	),
	// users
	'users' => array(
		'as' => 'users',
		'get' => function() {
			echo 'get users.';
		},
		'post' => function() {
			echo 'create user.';
		}
	),
	// user
	'user/{id|[0-9]+}' => array(
		'as' => 'user',
		'get' => function($id) {
			echo "get user with id $id";
		},
		'put' => function($id) {
			echo "update user with $id";
		},
		'delete' => function($id) {
			echo "delete user with $id";
		}
	),
	// user mapping (it's same as users/{id})
	'users/{id|[0-9]+}' => array(
		'sameAs' => 'user'
	),
	'user/{id|[0-9]+}/{name|[a-zA-Z0-9]+}' => array(
		'as' => 'test',
		'get' => function($id, $name) {
			echo "get user with $id and $name";
		}
	),
);