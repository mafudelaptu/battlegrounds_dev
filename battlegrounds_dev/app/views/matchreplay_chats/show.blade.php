@extends('layouts.scaffold')

@section('main')

<h1>Show Matchreplay_chat</h1>

<p>{{ link_to_route('matchreplay_chats.index', 'Return to all matchreplay_chats') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Matchreplay_id</th>
				<th>Match_id</th>
				<th>Name</th>
				<th>Time</th>
				<th>Msg</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $matchreplay_chat->matchreplay_id }}}</td>
					<td>{{{ $matchreplay_chat->match_id }}}</td>
					<td>{{{ $matchreplay_chat->name }}}</td>
					<td>{{{ $matchreplay_chat->time }}}</td>
					<td>{{{ $matchreplay_chat->msg }}}</td>
                    <td>{{ link_to_route('matchreplay_chats.edit', 'Edit', array($matchreplay_chat->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('matchreplay_chats.destroy', $matchreplay_chat->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
