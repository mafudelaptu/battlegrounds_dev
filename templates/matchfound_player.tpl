<div class="row-fluid">
	<div class="span1">
		<img alt="Avatar" src="<?php echo $v['Avatar'];?>" width="25" height="25">
	</div>
	<div class="span5">
		<?php echo $v['Name'];?>
	</div>
	<div class="span4">
		<span class="label"><?php echo $v['Rank'];?></span>(<span class="text-success"><?php echo $v['WinElo']?></span>/<span class="text-error"><?php echo $v['LoseElo']?></span>)&nbsp;<a href="help.php#winLoseElo" target="_blank" class="t" title="FAQ: How is the WinElo/LoseElo calculated?"><i class="icon-question-sign"></i></a>
	</div>
	<div class="span2" align="right">
		<div class="btn-group">
				  <a class="btn dropdown-toggle btn-mini" data-toggle="dropdown" href="#">
				    <i class="icon-eye-open"></i>
				    <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu">
				   <li><a href="#" target="_blank"><i class="icon-user"></i> show Profile</a></li>
				    <li class="divider"></li>
				    <li><a href="https://dotabuff.com/players/<?php echo $v['SteamID']?>" target="_blank">show DotaBuff-Profile</a></li>
				    <li><a href="<?php echo $v['ProfileURL']?>ProfileURL" target="_blank">show Steam-Profile</a></li>
				  </ul>
		</div>
	</div>
</div>


	