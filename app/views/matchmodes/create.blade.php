@extends('layouts.scaffold')

@section('main')

<h1>Create Matchmode</h1>

{{ Form::open(array('route' => 'admin.matchmodes.store')) }}
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


