@extends('layouts.global')

@section('layout')
	<header class="layout-header">
		<h1>
			<a href="{{ URL::to('/') }}">
				Laravel Packages <strong>Registry</strong>
			</a>
		</h1>
	</header>
	<main class="layout-content">
		@yield('content')
	</main>
	<footer class="layout-footer">
		&copy; {{ date('Y') }} - {{ HTML::link('https://github.com/Anahkiasen', 'Maxime Fabre') }} - {{ HTML::link('http://autopergamene.eu', 'Autopergamene') }}
	</footer>
@stop