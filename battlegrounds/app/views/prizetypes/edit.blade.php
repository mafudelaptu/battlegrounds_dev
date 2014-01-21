@extends('layouts.scaffold')

@section('main')

<h1>Edit Prizetype</h1>
{{ Form::model($prizetype, array('method' => 'PATCH', 'route' => array('prizetypes.update', $prizetype->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('count', 'Count:') }}
            {{ Form::input('number', 'count') }}
        </li>

        <li>
            {{ Form::label('type', 'Type:') }}
            {{ Form::text('type') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('prizetypes.show', 'Cancel', $prizetype->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
