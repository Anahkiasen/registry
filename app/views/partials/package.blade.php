<tr class="packages-list__package" data-id="{{ $package->slug }}">
	<td data-title="#" class="packages-list__key">{{ $key + $positionOffset }}</td>
	<td data-title="Name">{{ HTML::linkAction('PackagesController@package', $package->name, $package->slug) }}</td>
	<td data-title="Description" class="packages-list__description">{{ Str::words($package->description, 15) }}</td>
	<td data-title="Tags" class="packages-list__tags">
		@foreach ($package->keywords as $keyword)
			<span class="tag">{{ $keyword }}</span>
		@endforeach
	</td>
	<td data-title="Authors">{{ $package->maintainersList }}</td>
	<td data-title="Trust" class="packages-list__downloads">{{ $package->trust }}</td>
	<td data-title="Popularity" class="packages-list__downloads">{{ $package->popularity }}</td>
</tr>
