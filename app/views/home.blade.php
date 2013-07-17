@extends('layouts.layout')

@section('content')
	{{-- Search --}}
	<form method="POST" id="search">
		<input type="text" autocomplete="off" name="search" value="{{ Input::get('q') }}" placeholder="Type to search in the {{ $packages->count() }} packages available..." class="layout-search">
		<input type="reset" value="X" class="layout-search__reset">
	</form>

	{{-- Available packages --}}
	<table class="packages-list">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Description</th>
				<th class="packages-list__tags">Tag(s)</th>
				<th>Maintainer(s)</th>
				<th>Trust</th>
				<th>Popularity</th>
			</tr>
		</thead>
		<tbody>
			<tr class="packages-list__empty hidden">
				<td colspan="50">No results match your query</td>
			</tr>
			@each('partials.package', $packages, 'package')
		</tbody>
	</table>
@stop

@section('js')
	{{ Basset::show('home.js') }}
@stop
