<?php 

if(!isset($disableChat)){
	$disableChat = false;
}

?>
@if(!isset($height))
<?php 
$height = "200px";
?>
@endif
@if(!isset($active))
<?php 
$active = "";
?>
@endif

<div class="tab-pane {{$active}}" id="{{$chatname}}{{$room}}">
	<div class="row" style="height: {{$height}};">
		@if($disableChat)
		<div class="col-sm-12">
			<div class="conversation" style="max-height: {{$height}}; height: {{$height}};">

			</div>
		</div>
		
		@else
		<div class="col-sm-9">
			<div class="conversation" style="max-height: {{$height}}; height: {{$height}};">
				<div align="center" class="showPreviousMessagesBox">
					<button class="btn btn-link showPreviousMessages" data-chat="{{$chatname}}{{$room}}">show previous messages</button>
				</div>
			</div>
		</div>
		<div class="col-sm-3" style="max-height: {{$height}}; height: {{$height}};">
			<div class="count_chatusers">
				Users in Chat <span class="count">0</span><i class="fa fa-refresh t btn btn-link userlistRefreshButton" title="refresh userlist"></i> 
			</div>
			<div class="chatusers" style="max-height: {{$height}}; height: {{$height}};">
				
			</div>
		</div>
		@endif
		
	</div>
</div>
