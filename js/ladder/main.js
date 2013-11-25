/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			l(document.URL);
			if (document.URL.indexOf("/ladder.php") >= 0) {
				
				var ladder = getParameterByName("ladder");
				
				switch(ladder){
				case "Single5vs5Ladder":
				default:
					//initTable2("", 1, ""); // in der datei gelöst
					break;
				}
			}
		});


/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
/* Table initialisation */
function loadLadderDataTable(matchmodeID, section, matchTypeID){
	l("Start loadLadderData");
	l("Matchmode: "+matchmodeID);
	
	resultDivID = "#"+section+"Tabs"+matchmodeID;
	steamID = getParameterByName("ID");
	 $(resultDivID).block({ message: null });

			 l("hier");
			 $.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "ladder",
						mode : "loadLadderDataTable",
						matchModeID: matchmodeID,
						matchTypeID: matchTypeID,
						ID: steamID,
						section: section
					},
					success : function(result) {
						l(result);
						$(resultDivID).html(result.table);
						startDataNumber = result.startDataNumber;
						initTable(startDataNumber);
						$.unblockUI;
					}
				});
	
	l("End loadLadderData");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initTable(startDataNumber){
    
    if($(".LadderTable").length > 0){
           $('.LadderTable').dataTable( {
        "sPaginationType": "full_numbers",
         "aaSorting": [[ 2, "desc" ]],
//         "aoColumnDefs": [
//                          { 'bSortable': false, 'aTargets': [ 1 ] }
//                       ],
         "bSort": false,
         "bDestroy": true,
         "iDisplayStart": startDataNumber,
         
        "oLanguage": {
            "sLengthMenu": "_MENU_ Players per page",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Players"
        }
         } );
    }
	
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initTable2(matchModeID, matchTypeID, section){
    
    if($(".LadderTable").length > 0){
    	steamID = getParameterByName("ID");
           $('#ladderTable').dataTable( {
        	   "sPaginationType": "full_numbers",
  	         "aaSorting": [[ 2, "desc" ]],
//  	         "aoColumnDefs": [
//  	                          { 'bSortable': false, 'aTargets': [ 1 ] }
//  	                       ],
  	         "bSort": false,
  	         "bDestroy": true,
  	         
  	        "oLanguage": {
  	            "sLengthMenu": "_MENU_ Players per page",
  	            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Players"
  	        },
  	        //"iDisplayStart": 30,
  		 "bProcessing": true,
  	        "bServerSide": true,
  	        "sAjaxSource": "ajax.php",
  	        'fnServerParams': function ( aoData ) {
      		    aoData.push( { "name": "type", "value": "ladder"},
      		    		{ "name": "mode", "value": "loadLadderDataTable2"},
      		    		{ "name": "ID", "value": '76561198047012055'},
      		    		{ "name": "MMID", "value": ""},
      		    		{ "name": "MTID", "value": 1},
      		    		{ "name": "section", "value": 'Single5vs5'}
      		    )
      		}
         } );
    }
	
}