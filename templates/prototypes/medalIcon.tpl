<style type="text/css">
.medalIcon{
	width:32px;
	height:32px;
}
.medalIconBgClass{
	background-repeat: no-repeat;
}
.medalIconInnerText{
	font-weight:bold;
	color:whitesmoke;
	padding: 2px 0 0 11px;
}
</style>
{assign "medalIconBg" "background-image: url(img/medals/medal-`$leagueNameSimple`.png);"}
	
<div class="medalIcon medalIconBgClass t" title="{$leagueName}" style="{$medalIconBg} {$style}" >
	<div class="medalIconInnerText">{$leagueLvl}</div>
</div>
