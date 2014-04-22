
@if(!empty($data) && count($data)>0)
<div class="row" style="border-bottom:1px solid #eee; padding:10px 0; margin:0;">
	@if($data['user_id'] == 0)
	<div class="col-sm-12">
		### {{$data['msg']}} ### <span class="muted pull-right ">
				<small class="timeago" style="font-size:9px" title="{{$data['time']}}">{{$data['time']}}</small>
			</span>
	</div>
	@else
	<div class="col-sm-1" align="center">
		<a href="profile/76561198047012055" target="_blank">
			<img src="{{$data['avatar']}}" alt="Avatar of Mafu">
		</a>
		
	</div>
<div class="col-sm-11">
		<div>
			<a href="{{URL::to('profile/$data['user_id'])}}" target="_blank">
				<small><strong>{{$data['username']}}</strong></small>
			</a>
			<span class="muted pull-right ">
				<small class="timeago" style="font-size:9px" title="{{$data['time']}}">{{$data['time']}}</small>
			</span>
		</div>
		<div>
			{{$data['msg']}}		</div>
	</div>
		
	@endif
</div>
@endif