@extends('layouts.scaffold')

@section('main')

<h1>All Replay_dota2_heroes</h1>

<p>{{ link_to_route('replay_dota2_heroes.create', 'Add new replay_dota2_hero') }}</p>

@if ($replay_dota2_heroes->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($replay_dota2_heroes as $replay_dota2_hero)
				<tr>
					<td>{{{ $replay_dota2_hero->name }}}</td>
                    <td>{{ link_to_route('replay_dota2_heroes.edit', 'Edit', array($replay_dota2_hero->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('replay_dota2_heroes.destroy', $replay_dota2_hero->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no replay_dota2_heroes
@endif

@stop
