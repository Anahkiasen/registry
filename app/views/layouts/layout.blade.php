@extends('layouts.global')

@section('layout')
	<a href="{{ URL::to('/') }}">
		<header class="layout-header">
			<h1>Laravel Packages <strong>Registry</strong></h1>
		</header>
	</a>
	<main class="layout-content">
		@yield('content')
	</main>
	<footer class="layout-footer">
		<p>{{ HTML::linkAction('PackagesController@index', 'Packages') }} - {{ HTML::linkAction('MaintainersController@index', 'Maintainers') }}</p>
		<p>&copy; {{ date('Y') }} - {{ HTML::link('https://github.com/Anahkiasen', 'Maxime Fabre') }} - {{ HTML::link('http://autopergamene.eu', 'Autopergamene') }}</p>
	</footer>
@stop