@extends('layouts.layout')

@section('title')
	About the registry -
@stop

@section('content')
	<h1>About</h1>
	<h2>Packages</h2>
	<p>Packages are not hand-picked, to get your package in there all you need to do is add a <code>laravel</code> tag to it in your `composer.json` file.</p>

	<h2>Indexes</h2>
	<p>
		Packages get two indexes when added to the database : a <strong>trust</strong> index and a <strong>popularity</strong> index.<br>
		The <strong>trust index</strong> is based on the following factors :
	</p>
	<ul>
		<li>Presence of unit tests</li>
		<li>Status of said unit tests (failing/succeeding)</li>
		<li>Consistency of tests (how many of all builds have failed)</li>
		<li>How old the repository is</li>
		<li>When was the last commit</li>
		<li>Ratio open/closed issues</li>
	</ul>
	<p>The <strong>popularity index</strong> is based on the following factors :</p>
	<ul>
		<li>Number of downloads</li>
		<li>Stars, watchers and forks for the repository</li>
		<li>Days since last update</li>
		<li>Favorites for the Packagist entry</li>
	</ul>
	<p>Each of these factors is then assigned a weight according to how important it is in the index</p>

	<h2>Architecture</h2>
	<p>
		This registry binds the various APIs and services on which packages are hosted : Packagist, Github, Travis, Bitbucket.
		As a lot of endpoints need to be hit in order to build a good registry, and as indexes need to be calculated,
		the informations you see on the registry are not "live". The database is refreshed every night.
	</p>
	<p>The registry is a Laravel 4.1 application</p>
@stop
