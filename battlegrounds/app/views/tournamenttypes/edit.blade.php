@extends('layouts.scaffold')

@section('main')

<h1>Edit Tournamenttype</h1>
{{ Form::model($tournamenttype, array('method' => 'PATCH', 'route' => array('tournamenttypes.update', $tournamenttype->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('shortcut', 'Shortcut:') }}
            {{ Form::text('shortcut') }}
        </li>

        <li>
            {{ Form::label('active', 'Active:') }}
            {{ Form::input('number', 'active') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('tournamenttypes.show', 'Cancel', $tournamenttype->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
