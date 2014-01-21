@extends('layouts.scaffold')

@section('main')

<h1>Edit Matchreplay_dota2</h1>
{{ Form::model($matchreplay_dota2, array('method' => 'PATCH', 'route' => array('matchreplay_dota2s.update', $matchreplay_dota2->id))) }}
	<ul>
        <li>
            {{ Form::label('match_id', 'Match_id:') }}
            {{ Form::input('number', 'match_id') }}
        </li>

        <li>
            {{ Form::label('user_id', 'User_id:') }}
            {{ Form::text('user_id') }}
        </li>

        <li>
            {{ Form::label('hero', 'Hero:') }}
            {{ Form::text('hero') }}
        </li>

        <li>
            {{ Form::label('kills', 'Kills:') }}
            {{ Form::input('number', 'kills') }}
        </li>

        <li>
            {{ Form::label('deaths', 'Deaths:') }}
            {{ Form::input('number', 'deaths') }}
        </li>

        <li>
            {{ Form::label('assists', 'Assists:') }}
            {{ Form::input('number', 'assists') }}
        </li>

        <li>
            {{ Form::label('lvl', 'Lvl:') }}
            {{ Form::input('number', 'lvl') }}
        </li>

        <li>
            {{ Form::label('cs', 'Cs:') }}
            {{ Form::input('number', 'cs') }}
        </li>

        <li>
            {{ Form::label('denies', 'Denies:') }}
            {{ Form::input('number', 'denies') }}
        </li>

        <li>
            {{ Form::label('total_gold', 'Total_gold:') }}
            {{ Form::input('number', 'total_gold') }}
        </li>

        <li>
            {{ Form::label('first_blood_at', 'First_blood_at:') }}
            {{ Form::input('number', 'first_blood_at') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('matchreplay_dota2s.show', 'Cancel', $matchreplay_dota2->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
