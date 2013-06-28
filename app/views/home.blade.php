@extends('layouts.layout')

@section('content')
	<ol>
		@foreach ($packages as $package)
			<li>{{ $package->name }}</li>
		@endforeach
	</ol>
@stop