@extends('layouts.layout')

@section('content')
	<form method="POST">
		<input type="text" autocomplete="off" name="search" placeholder="Type to search..." class="layout-search">
	</form>
	<table class="packages-list">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Description</th>
				<th>Tags</th>
				<th>Authors</th>
				<th>Downloads</th>
			</tr>
		</thead>
		<tbody>
			<tr class="packages-list__empty">
				<td colspan="50">No results match your query</td>
			</tr>
			@foreach ($packages as $key => $package)
				<tr class="packages-list__package">
					<td data-title="#" class="packages-list__key">{{ $key + 1 }}</td>
					<td data-title="Name">{{ HTML::linkAction('PackagesController@getPackage', $package->name, $package->id) }}</td>
					<td data-title="Description" class="packages-list__description">{{ Str::words($package->description, 15) }}</td>
					<td data-title="Tags">{{ implode(', ', $package->tags) }}</td>
					<td data-title="Authors">{{ $package->maintainersList }}</td>
					<td data-title="Downloads" class="packages-list__downloads">{{ $package->downloads }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop