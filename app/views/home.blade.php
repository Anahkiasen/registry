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
				<th>Downloads</th>
			</tr>
		</thead>
		@foreach ($packages as $key => $package)
			<tr>
				<td class="packages-list__key">{{ $key + 1 }}</td>
				<td><a href="{{ $package->url }}">{{ $package->name }}</a></td>
				<td>{{ $package->downloads }} downloads</td>
			</tr>
		@endforeach
	</table>
@stop