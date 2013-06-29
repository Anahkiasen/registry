@extends('layouts.layout')

@section('title')
	{{ $package->name }} -
@stop

@section('content')
	<section class="package">
		<section class="package__informations">
			<h1>{{ $package->name }} <small>by {{ $package->maintainersList }}</small></h1>
			@include('partials.package-summary')
		</section>
		<hr>
		<h2>Versions</h2>
		@foreach ($package->versions as $version)
			<article class="package__version">
				<h3>{{ $version->version }} <small>{{ $version->relativeDate }}</small></h3>
				<code>
					<pre>"{{ $package->name }}": "{{ $version->version }}"</pre>
				</code>
			</article>
		@endforeach
	</section>
@stop