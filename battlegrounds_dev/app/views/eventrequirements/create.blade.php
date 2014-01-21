@extends('layouts.scaffold')

@section('main')

<h1>Create Eventrequirement</h1>

{{ Form::open(array('route' => 'eventrequirements.store')) }}
	<ul>
        <li>
            {{ Form::label('pointborder', 'Pointborder:') }}
            {{ Form::input('number', 'pointborder') }}
        </li>

        <li>
            {{ Form::label('skillbracketborder', 'Skillbracketborder:') }}
            {{ Form::input('number', 'skillbracketborder') }}
        </li>

        <li>
            {{ Form::label('winsborder', 'Winsborder:') }}
            {{ Form::input('number', 'winsborder') }}
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


