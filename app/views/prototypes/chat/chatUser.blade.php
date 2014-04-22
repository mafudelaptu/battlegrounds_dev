<?php 
$highlighAdmin = ""; 
if(!isset($data['avatar'])){
	$data['avatar'] = "";
}
if($data['admin']){
	$highlighAdmin = "admin";
}
$hideWhisper = "";
if($data['user_id'] == Auth::user()->id){
	$hideWhisper = "hide";
}
?>
<div class="{{$highlighAdmin}} {{$chatname}}userInChat btn-group btn-group-xs pointer" id="{{$chatname}}{{$data['user_id']}}">
	<div class="dropdown-toggle" data-toggle="dropdown">
		
		@include("prototypes.username", array("username"=>$data['username'], "avatar"=>$data['avatar'], "user_id"=>$data['user_id'], "truncateValue" => 15, "link" => false, "avatarWidth"=>"22"))

		<span class="caret hide"></span>
	</div>
	<ul class="dropdown-menu" role="menu">
		<li><a href="javascript:void(0)" data-name="{{$data['username']}}" data-avatar="{{$data['avatar']}}" data-id="{{$data['user_id']}}" class="whisper {{$hideWhisper}}">Whisper</a></li>
		
		<li class="divider"></li>
		<li><a href="{{URL::to('profile/'.$data['user_id'])}}" target="_blank" class="profile">show Arena-Profile</a></li>
		@if($isSteamGame)
		<li><a href="http://steamcommunity.com/profiles/{{$data['user_id']}}" target="_blank" class="steamprofile">show Steam-Profile</a></li>
		@endif
	</ul>
</div>