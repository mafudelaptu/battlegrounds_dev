@section('content')
<h1>{{$heading}}</h1>
@include("admin.queues.insertInQueue")

<h2>Set Ready</h2>
@include("admin.queues.setAllReadyInQueue")

@stop