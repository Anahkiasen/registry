@extends('layouts.layout')

@section('content')
	<h1>About</h1>
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
		<li>Favorites for the Packagist entry</li>
	</ul>
	<p>Each of these factors is then assigned a weight according to how important it is in the index</p>
@stop