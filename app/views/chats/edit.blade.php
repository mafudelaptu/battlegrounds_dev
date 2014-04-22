@extends('layouts.scaffold')

@section('main')

<h1>Edit Chat</h1>
{{ Form::model($chat, array('method' => 'PATCH', 'route' => array('chats.update', $chat->id))) }}
	<ul>
        <li>
            {{ Form::label('section', 'Section:') }}
            {{ Form::text('section') }}
        </li>

        <li>
            {{ Form::label('user_id', 'User_id:') }}
            {{ Form::text('user_id') }}
        </li>

        <li>
            {{ Form::label('message', 'Message:') }}
            {{ Form::textarea('message') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('chats.show', 'Cancel', $chat->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
