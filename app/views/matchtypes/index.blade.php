@extends('layouts.scaffold')

@section('main')

<h1>All Matchtypes</h1>

<p>{{ link_to_route('admin.matchtypes.create', 'Add new matchtype') }}</p>

@if ($matchtypes->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Active</th>
				<th>Playercount</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($matchtypes as $matchtype)
				<tr>
					<td>{{{ $matchtype->name }}}</td>
					<td>{{{ $matchtype->active }}}</td>
					<td>{{{ $matchtype->playercount }}}</td>
                    <td>{{ link_to_route('admin.matchtypes.edit', 'Edit', array($matchtype->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.matchtypes.destroy', $matchtype->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no matchtypes
@endif

@stop
