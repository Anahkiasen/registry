@extends('layouts.layout')

@section('content')
	<form method="POST" id="search">
		<input type="text" autocomplete="off" name="search" placeholder="Type to search..." class="layout-search">
		<input type="reset" value="X" class="layout-search__reset">
	</form>
	<table class="packages-list">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Description</th>
				<th class="packages-list__tags">Tag(s)</th>
				<th>Author(s)</th>
				<th>Downloads</th>
			</tr>
		</thead>
		<tbody>
			<tr class="packages-list__empty hidden">
				<td colspan="50">No results match your query</td>
			</tr>
			@foreach ($packages as $key => $package)
				<tr class="packages-list__package" data-id="{{ $package->id }}">
					<td data-title="#" class="packages-list__key">{{ $key + 1 }}</td>
					<td data-title="Name">{{ HTML::linkAction('PackagesController@getPackage', $package->name, $package->id) }}</td>
					<td data-title="Description" class="packages-list__description">{{ Str::words($package->description, 15) }}</td>
					<td data-title="Tags" class="packages-list__tags">
						@foreach ($package->tags as $tag)
							<span class="tag">{{ $tag }}</span>
						@endforeach
					</td>
					<td data-title="Authors">{{ $package->maintainersList }}</td>
					<td data-title="Downloads" class="packages-list__downloads">{{ $package->downloads }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop

@section('js')
	{{ Basset::show('home.js') }}
@stop