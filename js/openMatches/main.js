/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(function() {
if($("#openMatchesTable").length > 0){

           $('#openMatchesTable').dataTable( {
        "sPaginationType": "bootstrap",
        "aoColumns": [{ "sType": "html" },
                      null,
                      { "sType": "title-numeric" },
                      null,
                      { "sType": "html" },
                      null
                      ],
         "aaSorting": [[ 2, "desc" ]],
         "aoColumnDefs": [
                          { 'bSortable': false, 'aTargets': [ 1 ],
                        	'bSortable': true, 'aTargets': [ 0,2,3,4,5 ],
                           "sType": "num-html", "aTargets": [ 0, 3, 5 ]
                          }
                       ],

         "bDestroy": true,
//         "aoColumns": [
//                       { "sType": "html" },
//                       null,
//                       null,
//                       { "sType": "html" },
//                       null,
//                       { "sType": "html" },
//                     ],
        "oLanguage": {
            "sLengthMenu": "_MENU_ Matches per page",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Matches"
        }
         } );
    }


if(getParameterByName("openSubmissions")){
	  $('#openMatchesTable').dataTable( {
		  	"bDestroy": true,
	        "aaSorting": [[ 0, "desc" ]],
	    } );
}

});