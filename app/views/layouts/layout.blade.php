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
	@include('layouts.partials.footer')
@stop
