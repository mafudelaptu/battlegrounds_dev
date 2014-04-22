@extends('layouts.scaffold')

@section('main')

<h1>All News</h1>

<p>{{ link_to_route('admin.news.create', 'Add new news') }}</p>

@if ($news->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Title</th>
				<th>Content</th>
				<th>Order</th>
				<th>Active</th>
				<th>Show_date</th>
				<th>End_date</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($news as $news)
				<tr>
					<td>{{{ $news->title }}}</td>
					<td>{{ $news->content }}</td>
					<td>{{{ $news->order }}}</td>
					<td>{{{ $news->active }}}</td>
					<td>{{{ $news->show_date }}}</td>
					<td>{{{ $news->end_date }}}</td>
                    <td>{{ link_to_route('admin.news.edit', 'Edit', array($news->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.news.destroy', $news->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no news
@endif

@stop
