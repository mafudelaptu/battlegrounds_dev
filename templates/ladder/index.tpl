<!-- <div class="page-header"> -->
<!-- <h1>Ladder</h1> -->
<!-- </div> -->
<img src="img/ladder/ladders.jpg">
<div class="alert alert-warning">
	<strong>Note:</strong> Only players, who reached the Bronze-League will be recorded in the Ladder. Players that once reached Bronze-League, will stay inside it.
	
	
</div>
<ul class="nav nav-pills" style="background-color: #E9E9E9">
<!-- 	<li class="{$activeGlobalLadder}"> -->
<!--     <a href="?ladder=GlobalLadder">Global Ladder</a> -->
<!--   </li> -->
<!--   <li class="{$activeSingleLadder}"> -->
<!--     <a href="?ladder=Single5vs5Ladder">Single-5vs5-Ladder</a> -->
<!--   </li> -->
   <li class="{$active1vs1Ladder}">
    <a href="?ladder=1vs1Ladder">1vs1-Ladder</a>
  </li>
<!--    <li class="{$active3vs3Ladder}"> -->
<!--   	<a href="?ladder=3vs3Ladder">Single-3vs3-Ladder</a> -->
<!--   </li> -->
<!--   <li class="{$activeTeamLadder} disabled t" title="coming soon"> -->
<!--   	<a>Team-5vs5-Ladder</a> -->
<!--   </li> -->
</ul>
<hr style="border-top:1px solid #B4FF00" />
{**
{assign var="tableID" value=$section|cat:"TableGeneral"}
{include file="ladder/tablePrototypeSkillBracket.tpl" TableID=$tableID data=$generalData}
*}
<script type="text/javascript" charset="utf-8">

    $(document).ready(function(){
    	var ladder = getParameterByName("ladder");
		
		switch(ladder){
			case "1vs1Ladder":
				matchTypeID = 8;
				break;
		case "Single5vs5Ladder":
		default:
			matchTypeID = 8;
			break;
		}
		
		$("#ladderTable").dataTable({
 		   "sPaginationType": "full_numbers",
 	         "aaSorting": [[ 2, "desc" ]],
// 	         "aoColumnDefs": [
// 	                          { 'bSortable': false, 'aTargets': [ 1 ] }
// 	                       ],
 	         "bSort": false,
 	         "bDestroy": true,
 	         
 	        "oLanguage": {
 	            "sLengthMenu": "_MENU_ Players per page",
 	            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Players"
 	        },
 	      //  "iDisplayStart": 30,
 		 "bProcessing": true,
 	        "bServerSide": true,
 	        "sAjaxSource": "ajax.php",
 	        'fnServerParams': function ( aoData ) {
     		    aoData.push( { "name": "type", "value": "ladder"},
     		    		{ "name": "mode", "value": "loadLadderDataTableSkillBracket"},
     		    		{ "name": "ID", "value": '76561198047012055'},
     		    		{ "name": "MMID", "value": ""},
     		    		{ "name": "MTID", "value": matchTypeID},
     		    		{ "name": "section", "value": 'Single5vs5'}
     		    )
     		}
     });
    });
    </script>
<table id="ladderTable"
	class="table table-striped LadderTable" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>#</th>
			{if $smarty.const.NOLEAGUES == false}
				<th></th>
			{/if}
			<th>D2L-Points</th>
			<th>Player</th>
			<th>Points earned</th>
<!-- 			<th>Base-Points <a href="help.php#BasePoints" target="_blank"><i class="icon-question-sign"></i></a></th> -->
			<th>Wins</th>
			<th>Losses</th>
			<th>Win Rate</th>
			<th>Leaves</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>