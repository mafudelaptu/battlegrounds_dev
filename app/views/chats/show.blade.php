@extends('layouts.scaffold')

@section('main')

<h1>Show Chat</h1>

<p>{{ link_to_route('chats.index', 'Return to all chats') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Section</th>
				<th>User_id</th>
				<th>Message</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $chat->section }}}</td>
					<td>{{{ $chat->user_id }}}</td>
					<td>{{{ $chat->message }}}</td>
                    <td>{{ link_to_route('chats.edit', 'Edit', array($chat->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('chats.destroy', $chat->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
