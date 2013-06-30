<?php
use Packagist\Api\Client;
use Guzzle\Http\Client as Guzzle;

App::bind('packagist', function() {
	return new Client;
});

App::bind('guzzle', function() {
	return new Guzzle('https://packagist.org');
});

App::bind('github', function() {
	return new Guzzle('https://api.github.com/repos/');
});

App::bind('bitbucket', function() {
	return new Guzzle('https://bitbucket.org/api/1.0/repositories/');
});
