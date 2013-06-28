@extends('layouts.layout')

@section('content')
	<table class="table table-bordered table-condensed table-hover table-striped">
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
				<td><a href="{{ $package->url }}">{{ $package->name }}</a></td>
				<td>{{ $package->downloads }} downloads</td>
			</tr>
		@endforeach
	</table>
@stop