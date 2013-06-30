<section class="maintainer__informations">
	<figure class="maintainer__avatar">
		{{ HTML::image($maintainer->gravatar) }}
	</figure>
	<section class="maintainer__summary">
		<dl>
			<dt>Name</dt>
			<dd>{{ HTML::linkAction('MaintainersController@maintainer', $maintainer->name, $maintainer->slug) }}</dd>
			<dt>Packages</dt>
			<dd>{{ $maintainer->packagesNumber }} {{ Str::plural('package', $maintainer->packagesNumber) }} ({{ $maintainer->popularity }} popularity)</dd>
			<dt>Mail</dt>
			<dd>{{ HTML::mailto($maintainer->email) }}</dd>
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
</section>
