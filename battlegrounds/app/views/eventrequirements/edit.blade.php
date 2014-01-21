@extends('layouts.scaffold')

@section('main')

<h1>Edit Eventrequirement</h1>
{{ Form::model($eventrequirement, array('method' => 'PATCH', 'route' => array('eventrequirements.update', $eventrequirement->id))) }}
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
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('eventrequirements.show', 'Cancel', $eventrequirement->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
