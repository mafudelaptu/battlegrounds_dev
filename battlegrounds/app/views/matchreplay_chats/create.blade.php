@extends('layouts.scaffold')

@section('main')

<h1>Create Matchreplay_chat</h1>

{{ Form::open(array('route' => 'matchreplay_chats.store')) }}
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
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


