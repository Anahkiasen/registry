<?php
use Packagist\Api\Client;

App::bind('packagist', function($app) {
	return new Client;
});