@section('content')
<h1>{{$heading}}</h1>

@include("admin.bans.ban")

{{-- @include("admin.bans.chatban") --}}

@include("admin.bans.permaban")

@include("admin.bans.getBans")

@stop