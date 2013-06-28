@extends('layouts.global')

@section('layout')
	<header class="layout-header">
		<h1>Laravel Packages <strong>Registry</strong></h1>
	</header>
	<main class="layout-content">
		@yield('content')
	</main>
	<footer class="layout-footer">
		&copy; {{ date('Y') }} - {{ HTML::link('http://autopergamene.eu/', 'Maxime Fabre') }}
	</footer>
@stop