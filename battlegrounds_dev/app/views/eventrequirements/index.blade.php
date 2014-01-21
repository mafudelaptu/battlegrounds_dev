@extends('layouts.scaffold')

@section('main')

<h1>All Eventrequirements</h1>

<p>{{ link_to_route('eventrequirements.create', 'Add new eventrequirement') }}</p>

@if ($eventrequirements->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Pointborder</th>
				<th>Skillbracketborder</th>
				<th>Winsborder</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($eventrequirements as $eventrequirement)
				<tr>
					<td>{{{ $eventrequirement->pointborder }}}</td>
					<td>{{{ $eventrequirement->skillbracketborder }}}</td>
					<td>{{{ $eventrequirement->winsborder }}}</td>
                    <td>{{ link_to_route('eventrequirements.edit', 'Edit', array($eventrequirement->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('eventrequirements.destroy', $eventrequirement->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no eventrequirements
@endif

@stop
