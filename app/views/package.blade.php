@extends('layouts.layout')

@section('content')
	<section class="package">
		<section class="package__informations">
			<div class="package__informations__informations">
				<h1>{{ $package->name }} <small>by {{ $package->maintainersList }}</small></h1>
				<p class="package__description">{{ $package->description }}</p>
				<ul class="package__tags">
					<strong>Tags :</strong>
					@foreach ($package->tags as $tag)
						<li>{{ $tag }}</li>
					@endforeach
				</ul>
			</div>
			<div class="package__informations__downloads">
				<dl>
					<dt>Total Downloads</dt><dd>{{ $package->getInformations()->getDownloads()->getTotal() }}</dd>
					<dt>Monthly Downloads</dt><dd>{{ $package->getInformations()->getDownloads()->getMonthly() }}</dd>
					<dt>Daily Downloads</dt><dd>{{ $package->getInformations()->getDownloads()->getDaily() }}</dd>
				</dl>
			</div>
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