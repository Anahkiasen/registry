@extends('layouts.layout')

@section('content')
	<section class="package">
		<h1>{{ $package->name }} <small>by {{ $package->maintainersList }}</small></h1>
		<p class="package__description">{{ $package->description }}</p>
		<ul class="package__tags">
			<strong>Tags :</strong>
			@foreach ($package->tags as $tag)
				<li>{{ $tag }}</li>
			@endforeach
		</ul>
		<hr>
		<h2>Versions</h2>
		@foreach ($package->versions as $version)
			<article class="package__version">
				<h3>{{ $version->version }}</h3>
				<code>
					<pre>"anahkiasen/former": "{{ $version->version }}"</pre>
				</code>
			</article>
		@endforeach
	</section>
@stop