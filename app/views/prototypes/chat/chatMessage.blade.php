
@if(!empty($data) && count($data)>0)
<?php 

if(!isset($data['whisper'])){
	$data['whisper'] = false;
}

if($data['whisper'] == "true"){
	$highlight = "whisperMessage";
}
else{
	$highlight = "allChatMessage";
}

?>
<div class="chatMessage {{$highlight}}">
	<div class="pull-left">
		@if($data['user_id'] == 0)
		{{{$data['username']}}}
		@else
		@if($data['whisper'] == "true" && $data['user_id'] != Auth::user()->id)
			<i class="fa fa-comment t pointer inChatWhisper{{{$data['user_id']}}}" title="send response" data-name="{{{$data['username']}}}" data-avatar="{{{$data['avatar']}}}" data-id="{{{$data['user_id']}}}"></i>&nbsp;
		@endif
		@include("prototypes.username", array("username"=>htmlentities($data['username']), "avatar"=>htmlentities($data['avatar']), "user_id"=>htmlentities($data['user_id']), "link" => true, "avatarWidth"=>"16", "truncateValue"=>15))
		@endif
			@if($data['whisper'] == "false")
			:
			@endif
			&nbsp;
	</div>
	<small class="timeago chatMessageTime pull-right" title="{{{$data['time']}}}">{{$data['time']}}</small>
	@if($data['user_id'] == 0)
	### {{{$data['msg']}}} ###
	@else
	@if($data['whisper'] == "true")
	<i class="fa fa-caret-right"></i> 
	@include("prototypes.username", array("username"=>htmlentities($data['whisper_username']), "avatar"=>htmlentities($data['whisper_avatar']), "user_id"=>htmlentities($data['whisper_user_id']), "link" => true, "avatarWidth"=>"16")):&nbsp;
	@endif
	{{{$data['msg']}}}
	@endif
</div>

<div class="clearer"></div>
@endif