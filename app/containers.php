<?php
use Packagist\Api\Client;
use Guzzle\Http\Client as Guzzle;

App::bind('packagist', function($app) {
	return new Client;
});

App::bind('guzzle', function($app) {
	return new Guzzle('https://packagist.org');
});