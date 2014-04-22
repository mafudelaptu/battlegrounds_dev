@extends('layouts.scaffold')

@section('main')

<h1>Edit Replay_beststattype</h1>
{{ Form::model($replay_beststattype, array('method' => 'PATCH', 'route' => array('replay_beststattypes.update', $replay_beststattype->id))) }}
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
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('replay_beststattypes.show', 'Cancel', $replay_beststattype->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
