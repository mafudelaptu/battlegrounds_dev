@extends('layouts.scaffold')

@section('main')

<h1>Show Replay_dota2_hero</h1>

<p>{{ link_to_route('replay_dota2_heroes.index', 'Return to all replay_dota2_heroes') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $replay_dota2_hero->name }}}</td>
                    <td>{{ link_to_route('replay_dota2_heroes.edit', 'Edit', array($replay_dota2_hero->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('replay_dota2_heroes.destroy', $replay_dota2_hero->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
