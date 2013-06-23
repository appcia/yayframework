# YayFramework #

## Introduction ##

YayFramework is a little PHP framework. It attempts to provide a solid and easily understandable API for common 
PHP programming tasks, like database handling, caching, managing files and more. I wrote it for myself, but it's 
always good to share your code with others. Someday someone may find this project useful.

__YayFramework is in a really early stage, it's under heavy development, not capable of being used in a production 
environment.__

## Requirements ##

* PHP 5.4
* Some of the database connection classes require PDO and PDO drivers
* Rest coming soon

## Features ##

In progress.

## Structure ##

This may change during development.

```
	app/				default application directory
		action/			action classes (business logic)
		config/			configuration files
		controller/		controller classes
		model/			model classes
		view/			views
		App.php			a basic Application class
		bootstrap.php	init stuff here
	core/				YayFramework
	public/				directory accessible via http
		index.php		:)
```

## Installation ##

You don't need to install YayFramework, just simply clone this repo and use it. Using App.php and the app directory 
is a good start. Check bootstrap.php for the initialization of App class.

## Documentation ##

There is no documentation for YayFramework __yet__. You can check phpdoc comments if you want.

## Licensing ##

YayFramework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)