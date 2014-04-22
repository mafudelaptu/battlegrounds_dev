@if(!empty($data) && count($data) > 0)
<div id="upcomingEvents">
	<div class="row">
		<?php 
		$colSize = (int) 12 / $limit;
		?>
		@for($i=0; $i<$limit; $i++)
		<div class="col-sm-{{$colSize}}">
			@if(!empty($data[$i]) && count($data[$i]) > 0)
			<div class="row">
				<div class="col-sm-1">
					<div class="eventDay t" title="{{$data[$i]->start_day}}">
					<?php 
						$str = substr($data[$i]->start_day, 0,3);
						echo chunk_split($str, 1, " ");
					 ?>
					</div>
				</div>
				<div class="col-sm-3 eventStatus eventContent eventBorderRight eventBG"> 
					<?php //dd($data[$i]->status); ?>
					@if($data[$i]->status == "upcoming")
						<div class="upcoming">
							
						<?php 
							$date = new DateTime($data[$i]->start_at);
							echo $date->format("H:i");
						 ?>
						</div>
						<div class="timezone">
							<?php 
							echo Config::get('app.timezone');
							 ?>
						</div>
					@elseif($data[$i]->status == "check-in")
						<div class="check-in">
							{{HTML::link("events", $data[$i]->status)}}
						</div>
					@elseif($data[$i]->status == "check-in-closed")
						<div class="check-in-closed text-warning">
							{{$data[$i]->status}}
						</div>
					@else
					<div class="live">
						{{$data[$i]->status}}
					</div>
					@endif
				</div>
				<div class="col-sm-6 eventContent eventBG">
					{{$data[$i]->name}}
				</div>
				<div class="col-sm-1 eventContent eventBG">
					<?php 
						$popover = View::make('home.events.popover')
						->with("descr", $data[$i]->description)
						->with("data", $data[$i])
						->with("userAlreadyInEvent", false)
						->render();
					 ?>
					<i class="fa fa-info-circle pointer" id="eventPopover{{$i}}" data-toggle="popover" data-html="true" data-title='{{$data[$i]->name}}<button type="button" id="close" class="close" onclick="closeAllPopovers()">&times;</button>' data-content="{{{$popover}}}" data-placement="top" data-trigger="click" data-container="body"></i>
					
				</div>
			</div>
			@endif	
		</div>
		@endfor
	</div>
	
</div>
@else
<div class="alert alert-info">
	no upcoming Events
</div>
@endif