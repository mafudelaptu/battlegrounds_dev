@extends('layouts.scaffold')

@section('main')

<h1>All Replay_beststattypes</h1>

<p>{{ link_to_route('replay_beststattypes.create', 'Add new replay_beststattype') }}</p>

@if ($replay_beststattypes->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Active</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($replay_beststattypes as $replay_beststattype)
				<tr>
					<td>{{{ $replay_beststattype->name }}}</td>
					<td>{{{ $replay_beststattype->active }}}</td>
                    <td>{{ link_to_route('replay_beststattypes.edit', 'Edit', array($replay_beststattype->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('replay_beststattypes.destroy', $replay_beststattype->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no replay_beststattypes
@endif

@stop
