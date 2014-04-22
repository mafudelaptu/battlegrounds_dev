<span class="input-group-addon cancelWhisper t pointer" title="cancel whispering">
	<i class="fa fa-times"></i>
</span>
<span class="input-group-addon t switch pointer" title="change back to All-Chat" data-mode="AllChat">
	<i class="fa fa-caret-right"></i>&nbsp;<i class="fa fa-comments"></i>
</span>
<span class="input-group-addon whisperUser" data-id="{{{$user_id}}}" data-name="{{{$name}}}" data-avatar="{{{$avatar}}}">
	@include("prototypes.username", array("username"=>$name, "user_id"=>$user_id, "credits"=>0, "truncateValue"=>10, "avatar"=>$avatar, "link"=>false, "avatarWidth"=>16))
</span>
