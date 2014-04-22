<?php 
	$textClass = "";
	$iconClass = "";
	$titleText = "";
	$titleName = "";
	$border = GlobalSetting::getCreditBorders();
	if(empty($truncateValue)){
		$truncateValue = 0;
	}
	if(!isset($credits)){
		$credits = 0;
	}
	if(!isset($link)){
		$link = true;
	}
	if(!isset($avatarWidth)){
		$avatarWidth = "22";
	}
	
 ?>
	
@if ($credits >= $border['bronze'] && $credits < $border['silver'])
	<?php  $textClass = "text-bronze"; ?>
@elseif ($credits >= $border['silver'] && $credits < $border['gold'])
	<?php $textClass = "text-silver";  ?>
@elseif ($credits >= $border['gold'])
	<?php $textClass = "text-gold";  ?>
@endif

@if( strlen($username) >= $truncateValue && $truncateValue != 0)
	<?php $titleName = $username; ?>
@endif

<span class="t {{$textClass}}" title="{{$titleName}}">

	{{-- */$titleText = "earned Creditpoints: $credits"; /*--}}
	@if($link)
	<a href="{{URL::to('profile/'.$user_id);}}" target="_blank">
	@endif
		@if($avatar != "")
			<img src="{{$avatar}}" alt="Avatar" width="{{$avatarWidth}}">&nbsp;
		@endif
			@if($truncateValue > 0)
			{{Str::limit($username, $truncateValue)}}
		@else
			{{$username}}
		@endif
	@if($link)
	</a>
	@endif
	@if( $credits >= $border['bronze'])
	<span class="t" title="{{$titleText}}">
		@include("icons.credit")
	</span>
	@endif
</span>