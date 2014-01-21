@extends('layouts.scaffold')

@section('main')

<h1>Show Replay_beststattype</h1>

<p>{{ link_to_route('replay_beststattypes.index', 'Return to all replay_beststattypes') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
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
	</tbody>
</table>

@stop
