<section class="package__summary">
	<dl>
		@if ($package->description)
			<dt>Description</dt>
			<dd>{{ $package->description }}</dd>
		@endif
		<dt>Tags</dt>
		<dd>
			@if ($package->keywords)
				@foreach ($package->keywords as $keyword)
					<li class="tag">
						<a href="{{ URL::action('PackagesController@index') }}?q={{ $keyword }}">{{ $keyword }}</a>
					</li>
				@endforeach
			@else
				No tags
			@endif
		</dd>
		<dt>Build status</dt>
		<dd>
			<span class="package__status package__status--{{ $package->travis }}">{{ $package->travis }}</span>
		</dd>
		<dt>See on</dt>
		<dd class="package__links">
			<a target="_blank" href="{{ $package->packagist }}"><i class="icon-box"></i> Packagist</a>
			<a target="_blank" href="{{ $package->repository }}"><i class="icon-github"></i> Github</a>
		</dd>
		<dt>Trust index</dt>
		<dd>{{ $package->trust }}</dd>
		<dt>Popularity</dt>
		<dd>
			<strong>Stars</strong> : {{ $package->watchers }}<br>
			<strong>Forks</strong> : {{ $package->forks }}<br>
			<strong>Favorites</strong> : {{ $package->favorites }}
		</dd>
		<dt>Downloads</dt>
		<dd>
			<strong>Total</strong> : {{ $package->downloads_total }}<br>
			<strong>Monthly</strong> : {{ $package->downloads_monthly }}<br>
			<strong>Today</strong> : {{ $package->downloads_daily }}
		</dd>
	</dl>
</section>
