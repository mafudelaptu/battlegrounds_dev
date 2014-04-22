@if($matchState == "closed" || $matchState == "canceled")
	
@if(strpos($pointsChange, '-') === 0)
		<?php 
			$textClass = "danger";
			$textAddition = "";
		?>
	@else

		@if($pointsChange != "0" && $pointsChange != 0)
		<?php 
			$textClass = "success";
			$textAddition = "+";
		?>
		@else
<?php 
			$textClass = "";
			$textAddition = "";
			$pointsChange = "";
		?>
		@endif
		
	@endif
	<?php 
		$tipsy_text = $textAddition.$pointsChange; 
	?>
	<div class="label label-matchpage t">
	{{$points}}
	<span class="text-{{$textClass}}">
{{$tipsy_text}}
</span>
</div>&nbsp;

@else
	<?php 
		$tipsy_text = "+".$winPoints."/"."-".$losePoints;
	 ?>
	 <div class="label label-matchpage t" title="{{$tipsy_text}}">
	{{$points}}
</div>
@endif


