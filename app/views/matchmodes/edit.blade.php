@extends('layouts.scaffold')

@section('main')

<h1>Edit Matchmode</h1>
{{ Form::model($matchmode, array('method' => 'PATCH', 'route' => array('admin.matchmodes.update', $matchmode->id))) }}
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
            {{ Form::label('descr', 'Descr:') }}
            {{ Form::textarea('descr') }}
        </li>

        <li>
            {{ Form::label('active', 'Active:') }}
            {{ Form::input('number', 'active') }}
        </li>

        <li>
            {{ Form::label('bonus', 'Bonus:') }}
            {{ Form::input('number', 'bonus') }}
        </li>

        <li>
            {{ Form::label('bonus_active', 'Bonus_active:') }}
            {{ Form::input('number', 'bonus_active') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('admin.matchmodes.show', 'Cancel', $matchmode->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
