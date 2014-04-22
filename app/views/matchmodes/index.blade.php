@extends('layouts.scaffold')

@section('main')

<h1>All Matchmodes</h1>

<p>{{ link_to_route('admin.matchmodes.create', 'Add new matchmode') }}</p>

@if ($matchmodes->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Shortcut</th>
				<th>Descr</th>
				<th>Active</th>
				<th>Bonus</th>
				<th>Bonus_active</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($matchmodes as $matchmode)
				<tr>
					<td>{{{ $matchmode->name }}}</td>
					<td>{{{ $matchmode->shortcut }}}</td>
					<td>{{{ $matchmode->descr }}}</td>
					<td>{{{ $matchmode->active }}}</td>
					<td>{{{ $matchmode->bonus }}}</td>
					<td>{{{ $matchmode->bonus_active }}}</td>
                    <td>{{ link_to_route('admin.matchmodes.edit', 'Edit', array($matchmode->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.matchmodes.destroy', $matchmode->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no matchmodes
@endif

@stop
