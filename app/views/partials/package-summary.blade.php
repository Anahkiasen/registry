<section class="package__summary">
	<dl>
		@if ($package->description)
			<dt>Description</dt>
			<dd>{{ $package->description }}</dd>
		@endif

		@if ($package->keywords)
			<dt>Tags</dt>
			<dd>
				@foreach ($package->keywords as $keyword)
					<li class="tag">
						<a href="{{ URL::action('PackagesController@index') }}?q={{ $keyword }}">{{ $keyword }}</a>
					</li>
				@endforeach
			</dd>
		@endif

		<dt>Build status</dt>
		<dd>
			<span class="package__status package__status--{{ $package->travisBuild }}">{{ $package->travisBuild }}</span>
		</dd>

		<dt>See on</dt>
		<dd class="package__links">
			<a target="_blank" href="{{ $package->packagist }}"><i class="icon-box"></i> Packagist</a>
			<a target="_blank" href="{{ $package->repository }}"><i class="icon-github"></i> Github</a>
		</dd>

		<dt>Indexes</dt>
		<dd>
			<strong>Trust</strong> : {{ $package->trust }}<br>
			<strong>Popularity</strong> : {{ $package->popularity }}
		</dd>

		<dt>Popularity</dt>
		<dd>
			<strong>Stars</strong> : {{ $package->watchers }}<br>
			<strong>Forks</strong> : {{ $package->forks }}<br>
			<strong>Favorites</strong> : {{ $package->favorites }}
		</dd>

		<dt>Trust</dt>
		<dd>
			<strong>Seniority</strong> : {{ $package->created_at->diffInDays() }} days<br>
			<strong>Last updated:</strong> : {{ $package->pushed_at->toDateString() }}<br>
			<strong>% of closed issues</strong> : {{ $package->issues }}
		</dd>

		<dt>Downloads</dt>
		<dd>
			<strong>Total</strong> : {{ $package->downloads_total }}<br>
			<strong>Monthly</strong> : {{ $package->downloads_monthly }}<br>
			<strong>Today</strong> : {{ $package->downloads_daily }}
		</dd>
	</dl>
</section>
