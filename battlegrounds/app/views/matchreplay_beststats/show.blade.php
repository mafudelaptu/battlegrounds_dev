@extends('layouts.scaffold')

@section('main')

<h1>Show Matchreplay_beststat</h1>

<p>{{ link_to_route('matchreplay_beststats.index', 'Return to all matchreplay_beststats') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Matchreplay_id</th>
				<th>Match_id</th>
				<th>User_id</th>
				<th>Replay_beststattype_id</th>
				<th>Value</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $matchreplay_beststat->matchreplay_id }}}</td>
					<td>{{{ $matchreplay_beststat->match_id }}}</td>
					<td>{{{ $matchreplay_beststat->user_id }}}</td>
					<td>{{{ $matchreplay_beststat->replay_beststattype_id }}}</td>
					<td>{{{ $matchreplay_beststat->value }}}</td>
                    <td>{{ link_to_route('matchreplay_beststats.edit', 'Edit', array($matchreplay_beststat->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('matchreplay_beststats.destroy', $matchreplay_beststat->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
