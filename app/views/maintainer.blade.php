@extends('layouts.layout')

@section('content')
	<section class="maintainer">
		<h1>{{ $maintainer->name }} <small>{{ sizeof($maintainer->packages) }} packages</small></h1>
		<section class="maintainer__summary">
			<dl>
				<dt>Mail</dt>
				<dd>{{ $maintainer->email }}</dd>
				@if ($maintainer->homepage)
					<dt>Homepage</dt>
					<dd>{{ $maintainer->homepage }}</dd>
				@endif
				<dt>See on</dt>
				<dd class="maintainer__links">
					<a target="_blank" href="{{ $maintainer->github }}"><i class="icon-github"></i> Github</a>
				</dd>
			</dl>
		</section>
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