@extends('layouts.scaffold')

@section('main')

<h1>All Tournamenttypes</h1>

<p>{{ link_to_route('tournamenttypes.create', 'Add new tournamenttype') }}</p>

@if ($tournamenttypes->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Shortcut</th>
				<th>Active</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($tournamenttypes as $tournamenttype)
				<tr>
					<td>{{{ $tournamenttype->name }}}</td>
					<td>{{{ $tournamenttype->shortcut }}}</td>
					<td>{{{ $tournamenttype->active }}}</td>
                    <td>{{ link_to_route('tournamenttypes.edit', 'Edit', array($tournamenttype->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('tournamenttypes.destroy', $tournamenttype->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no tournamenttypes
@endif

@stop
