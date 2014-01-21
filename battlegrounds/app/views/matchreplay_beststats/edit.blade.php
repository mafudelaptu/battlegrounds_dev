@extends('layouts.scaffold')

@section('main')

<h1>Edit Matchreplay_beststat</h1>
{{ Form::model($matchreplay_beststat, array('method' => 'PATCH', 'route' => array('matchreplay_beststats.update', $matchreplay_beststat->id))) }}
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
            {{ Form::label('user_id', 'User_id:') }}
            {{ Form::text('user_id') }}
        </li>

        <li>
            {{ Form::label('replay_beststattype_id', 'Replay_beststattype_id:') }}
            {{ Form::input('number', 'replay_beststattype_id') }}
        </li>

        <li>
            {{ Form::label('value', 'Value:') }}
            {{ Form::input('number', 'value') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('matchreplay_beststats.show', 'Cancel', $matchreplay_beststat->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
