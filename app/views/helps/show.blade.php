@extends('layouts.scaffold')

@section('main')

<h1>Show Help</h1>

<p>{{ link_to_route('admin.helps.index', 'Return to all helps') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Type</th>
				<th>Caption</th>
				<th>Content</th>
				<th>Order</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $help->type }}}</td>
					<td>{{{ $help->caption }}}</td>
					<td>{{ $help->content }}</td>
					<td>{{{ $help->order }}}</td>
					<td>{{{ $help->active }}}</td>
                    <td>{{ link_to_route('admin.helps.edit', 'Edit', array($help->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.helps.destroy', $help->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
