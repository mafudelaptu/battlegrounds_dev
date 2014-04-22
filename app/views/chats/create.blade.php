@extends('layouts.scaffold')

@section('main')

<h1>Create Chat</h1>

{{ Form::open(array('route' => 'chats.store')) }}
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


