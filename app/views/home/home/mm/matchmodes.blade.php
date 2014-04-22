<div class="box" id="selectMatchmodesPanel">
	<div class="box_title pointer" id="selectMatchmodesButton">
		select match mode
	</div>
	<div class="box_content" id="selectMatchmodesContent" style="display:none">
		<div id="selectedMatchmodesCheckboxes">
			@foreach($matchmodes as $mm)
			<?php 
			if(!empty($_COOKIE['selectedMatchmodes'])){
				if(in_array($mm->id, $_COOKIE['selectedMatchmodes'])){
					$badgeClass = "info";
					$checked = "checked='checked'";
				}
				else{
					$badgeClass = "default";
					$checked = "";
				}
			}
			else{
				$badgeClass = "default";
				$checked = "";
			}
			?>
			<label class="checkbox">
				<input type="checkbox" value="{{$mm->id}}" name="selectedMatchmode" {{$checked}}><span class="badge badge-{{$badgeClass}} t" title="{{$mm->name}}">{{$mm->shortcut}}</span>
			</label>
			
			@endforeach
			
		</div>
	</div>
</div>
