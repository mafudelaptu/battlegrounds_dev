@extends('layouts.scaffold')

@section('main')

<h1>Show Matchreplay_dota2</h1>

<p>{{ link_to_route('matchreplay_dota2s.index', 'Return to all matchreplay_dota2s') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Match_id</th>
				<th>User_id</th>
				<th>Hero</th>
				<th>Kills</th>
				<th>Deaths</th>
				<th>Assists</th>
				<th>Lvl</th>
				<th>Cs</th>
				<th>Denies</th>
				<th>Total_gold</th>
				<th>First_blood_at</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $matchreplay_dota2->match_id }}}</td>
					<td>{{{ $matchreplay_dota2->user_id }}}</td>
					<td>{{{ $matchreplay_dota2->hero }}}</td>
					<td>{{{ $matchreplay_dota2->kills }}}</td>
					<td>{{{ $matchreplay_dota2->deaths }}}</td>
					<td>{{{ $matchreplay_dota2->assists }}}</td>
					<td>{{{ $matchreplay_dota2->lvl }}}</td>
					<td>{{{ $matchreplay_dota2->cs }}}</td>
					<td>{{{ $matchreplay_dota2->denies }}}</td>
					<td>{{{ $matchreplay_dota2->total_gold }}}</td>
					<td>{{{ $matchreplay_dota2->first_blood_at }}}</td>
                    <td>{{ link_to_route('matchreplay_dota2s.edit', 'Edit', array($matchreplay_dota2->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('matchreplay_dota2s.destroy', $matchreplay_dota2->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
