<tr class="packages-list__package" data-id="{{ $package->slug }}">
	<td data-title="#" class="packages-list__key">{{ $key + $positionOffset }}</td>
	<td data-title="Name">{{ HTML::linkAction('PackagesController@package', $package->name, $package->slug) }}</td>
	<td data-title="Description" class="packages-list__description">{{ Str::words($package->description, 15) }}</td>
	<td data-title="Tags" class="packages-list__tags">
		@foreach ($package->tags as $tag)
			<span class="tag">{{ $tag }}</span>
		@endforeach
	</td>
	<td data-title="Authors">{{ $package->maintainersList }}</td>
	<td data-title="Downloads" class="packages-list__downloads">{{ $package->popularity }}</td>
</tr>