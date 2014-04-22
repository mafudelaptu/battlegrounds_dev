@extends('layouts.scaffold')

@section('main')

<h1>All Matchreplay_chats</h1>

<p>{{ link_to_route('matchreplay_chats.create', 'Add new matchreplay_chat') }}</p>

@if ($matchreplay_chats->count())
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
			@foreach ($matchreplay_chats as $matchreplay_chat)
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
			@endforeach
		</tbody>
	</table>
@else
	There are no matchreplay_chats
@endif

@stop
