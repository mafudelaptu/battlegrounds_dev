@extends('layouts.scaffold')

@section('main')

<h1>Edit Matchtype</h1>
{{ Form::model($matchtype, array('method' => 'PATCH', 'route' => array('admin.matchtypes.update', $matchtype->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('active', 'Active:') }}
            {{ Form::input('number', 'active') }}
        </li>

        <li>
            {{ Form::label('playercount', 'Playercount:') }}
            {{ Form::input('number', 'playercount') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('admin.matchtypes.show', 'Cancel', $matchtype->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
