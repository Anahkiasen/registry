@extends('layouts.layout')

@section('content')
	<table class="table">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Downloads</th>
			</tr>
		</thead>
		@foreach ($packages as $key => $package)
			<tr>
				<td>{{ $key }}</td>
				<td>{{ $package->name }}</td>
				<td>{{ $package->downloads }} downloads</td>
			</tr>
		@endforeach
	</table>
@stop