@extends('layouts.layout')

@section('title')
	{{ $package->name }} -
@stop

@section('content')
	<section class="package">
		<section class="package__informations">
				<h1>{{ $package->name }} <small>by {{ $package->maintainersList }}</small></h1>
				<section class="package__summary">
					<dl>
						<dt>Description</dt>
						<dd>{{ $package->description }}</dd>
						<dt>Tags</dt>
						<dd>
							@foreach ($package->tags as $tag)
								<li class="tag">{{ $tag }}</li>
							@endforeach
						</dd>
						<dt>See on</dt>
						<dd class="package__links">
							<a target="_blank" href="{{ $package->packagist }}"><i class="icon-box"></i> Packagist</a>
							<a target="_blank" href="{{ $package->github }}"><i class="icon-github"></i> Github</a>
						</dd>
						<dt>Downloads</dt>
						<dd>
							<strong>Total Downloads</strong> : {{ $package->downloads_total }}<br>
							<strong>Monthly Downloads</strong> : {{ $package->downloads_monthly }}<br>
							<strong>Daily Downloads</strong> : {{ $package->downloads_daily }}
						</dd>
					</dl>
				</section>
		</section>
		<hr>
		<h2>Versions</h2>
		@foreach ($package->versions as $version)
			<article class="package__version">
				<h3>{{ $version->version }} <small>{{ $version->created_at->diffForHumans(Carbon\Carbon::now()) }}</small></h3>
				<code>
					<pre>"{{ $package->name }}": "{{ $version->version }}"</pre>
				</code>
			</article>
		@endforeach
	</section>
@stop