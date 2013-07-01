@extends('layouts.layout')

@section('content')
	<h1>About</h1>
	<h2>Indexes</h2>
	<p>
		Packages get two indexes when added to the database : a <strong>trust</strong> index and a <strong>popularity</strong> index.<br>
		The <strong>trust index</strong> is based on the following factors : presence of unit tests, status of said unit tests (failing/succeeding), how old the repository is, and when was the last commit.<br>
		The <strong>popularity index</strong> is based on the following factors : number of downloads, stars, watchers and forks for the repository, and favorites for the Packagist entry.
	</p>
	<p>Each of these factors is then assigned a weight according to how important it is in the index</p>
@stop