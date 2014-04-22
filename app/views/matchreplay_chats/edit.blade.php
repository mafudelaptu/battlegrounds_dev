@extends('layouts.scaffold')

@section('main')

<h1>Edit Matchreplay_chat</h1>
{{ Form::model($matchreplay_chat, array('method' => 'PATCH', 'route' => array('matchreplay_chats.update', $matchreplay_chat->id))) }}
	<ul>
        <li>
            {{ Form::label('matchreplay_id', 'Matchreplay_id:') }}
            {{ Form::input('number', 'matchreplay_id') }}
        </li>

        <li>
            {{ Form::label('match_id', 'Match_id:') }}
            {{ Form::input('number', 'match_id') }}
        </li>

        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('time', 'Time:') }}
            {{ Form::text('time') }}
        </li>

        <li>
            {{ Form::label('msg', 'Msg:') }}
            {{ Form::textarea('msg') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('matchreplay_chats.show', 'Cancel', $matchreplay_chat->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
