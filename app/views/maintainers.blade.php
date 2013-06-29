@extends('layouts.layout')

@section('content')
	<h1>Maintainers <small>The faces of Laravel</small></h1>
	<ul class="maintainers-list">
		@foreach ($maintainers as $maintainer)
			<li>
				@include('partials.maintainer-summary')
			</li>
		@endforeach
	</ul>
@stop