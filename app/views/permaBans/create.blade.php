@extends('layouts.scaffold')

@section('main')

<h1>Create PermaBan</h1>

{{ Form::open(array('route' => 'admin.permaBans.store')) }}
	<ul>
        <li>
            {{ Form::label('user_id', 'User_id:') }}
            {{ Form::text('user_id') }}
        </li>

        <li>
            {{ Form::label('banlistreason_id', 'Banlistreason_id:') }}
            {{ Form::input('number', 'banlistreason_id', 2) }}
        </li>

        <li>
            {{ Form::label('banned_at', 'Banned_at:') }}
            <?php 
               $objDateTime = new DateTime('NOW');

             ?>
            {{ Form::text('banned_at', $objDateTime->format("Y-m-d H:i:s")) }}
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


