/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
/* Table initialisation */
$(document).ready(function() {
    if($('#lastMatchesTable').length > 0){
       $('#lastMatchesTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
         "aaSorting": [[ 1, "desc" ]],
         "aoColumns": [null,
                       { "sType": "title-numeric" },
                       null,
                       null,
                       null
                       ],
        "oLanguage": {
            "sLengthMenu": "_MENU_ Matches per page",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Matches"
        }
    } );
    
    }
	
} );