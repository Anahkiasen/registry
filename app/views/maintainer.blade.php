@extends('layouts.layout')

@section('content')
	<section class="maintainer">
		<h1>{{ $maintainer->name }} <small>{{ sizeof($maintainer->packages) }} packages</small></h1>
		@include('partials.maintainer-summary')
		<hr>
		<h2>Packages</h2>
		@foreach ($maintainer->packages as $package)
			<article class="maintainer__package">
				<h3>{{ HTML::linkAction('PackagesController@package', $package->name, $package->slug) }} <small>last updated {{ $package->relativeDate }}</small></h3>
				@include('partials.package-summary')
			</article>
		@endforeach
	</section>
@stop