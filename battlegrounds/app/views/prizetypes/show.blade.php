@extends('layouts.scaffold')

@section('main')

<h1>Show Prizetype</h1>

<p>{{ link_to_route('prizetypes.index', 'Return to all prizetypes') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Count</th>
				<th>Type</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $prizetype->name }}}</td>
					<td>{{{ $prizetype->count }}}</td>
					<td>{{{ $prizetype->type }}}</td>
                    <td>{{ link_to_route('prizetypes.edit', 'Edit', array($prizetype->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('prizetypes.destroy', $prizetype->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
