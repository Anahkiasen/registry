@extends('layouts.layout')

@section('content')
	<form method="POST">
		<input type="text" name="search" placeholder="Type to search..." class="layout-search">
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
			@foreach ($packages as $key => $package)
				<tr>
					<td class="packages-list__key">{{ $key + 1 }}</td>
					<td>{{ HTML::linkAction('PackagesController@getPackage', $package->name, $package->id) }}</td>
					<td class="packages-list__description">{{ Str::words($package->description, 15) }}</td>
					<td data-tags="{{{ $package->getOriginal('tags') }}}">{{ implode(', ', $package->tags) }}</td>
					<td>{{ $package->maintainersList }}</td>
					<td class="packages-list__downloads">{{ $package->downloads }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop