<?php
use Guzzle\Http\Client as Guzzle;
use Packagist\Api\Client as Packagist;

//////////////////////////////////////////////////////////////////////
//////////////////////////// IOC CONTAINERS //////////////////////////
//////////////////////////////////////////////////////////////////////

App::bind('packagist', function() {
	return new Packagist;
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

App::bind('travis', function() {
	return new Guzzle('https://api.travis-ci.org/repos/');
});
