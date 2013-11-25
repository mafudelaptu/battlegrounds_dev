/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			l(document.URL);
			
			if (document.URL.indexOf("/match.php?") > 0 || document.URL.indexOf("/eventMatch.php?") > 0 ) {
				initReplayButton();
				$("a[data-toggle='popover']").popover({
					html: true,
					placement: 'top',
					trigger: 'hover'
				});
				
				initChat("match", parseInt(getParameterByName("matchID")), "matchChat"); /* Start the inital request */
				
			}
});
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initReplayButton(){
	var button = $("#matchSubmitReplay");
	var disabled = button.hasClass("disabled");

	if(!disabled){
		button.click(function(){
			l("init replay button");
			var matchID = getParameterByName("matchID");
			$('#replayUpload').fileupload({
		        dataType: 'json',
		        acceptFileTypes: /(zip)|(rar)$/i,  // Allowed File Types
		        disableValidation: false,
		        done: function (e, data) {
		        	$("#progressReplay").hide();
		        	
		            $.each(data.result.files, function (index, file) {
		            	$("#replayUploaded").html(file.name);
		            	
		            	
		            });
		            fileUploaded = data.result.files[0];
		            
		            	$.ajax({
			        		url: 'ajax.php',
			        		type: "POST",
			        		dataType: 'json',
			        		data: {type:"uploads", mode: "moveReplayFile", ID:matchID, fileName: fileUploaded.name},
			        		success: function(result2) {
			        			l(result2);

			        			if(result2.status == "fuckYou"){
			        				$('#replayUploadModal').modal('hide');
			        				text = "<div align='center'><h4>Sorry, your zip-file is broken or not in the right format</h4> "
										+ "<p>please read the tutorial for uploading a zip-file with parsed replay-files in it.</p>";
								bootbox.alert(text);
			        			}
			        			
			        			if(result2.status == "replayInDB"){
			        				$('#replayUploadModal').modal('hide');
			        				text = "<div align='center'><h4>Sorry</h4> "
										+ "<p>An other player uploaded the replay-files faster than you. The statistics are already there for this match!</p>";
								bootbox.alert(text, function() {
									window.location.reload();
								});
			        			}
			        			
			        			if(result2.status == "wrongReplay"){
			        				$('#replayUploadModal').modal('hide');
			        				text = "<div align='center'><h4>Warning!</h4> "
										+ "<p>You tried to upload a wrong replay. Next time be sure you submit the right one!</p>";
								bootbox.alert(text, function() {
									window.location.reload();
								});
			        			}
			        			
			        			if(result2.status == true){
			        				$('#replayUploadModal').modal('hide');
			        				text = "<div align='center'><h4>You successfully uploaded the replay-files</h4> "
										+ "<p>As a reward you get <strong class='text-success'>+1</strong> Credit-Point</p>";
									bootbox.alert(text, function() {
										window.location.reload();
									});
			        			}
			        			
			        		}
			        	
			            });
		           
		        },
		        add:function(e, data){
		        	var file = data.files[0];
		        	l(file);
		        	if(file.type == "application/zip" || file.type == "application/x-zip-compressed" || file.type == ""){
		        		data.submit();
		        	}
		            else{
		            	alert("wrong documenttype for upload. Just zip-files allowed!");
		            }
		        },
				progressall: function (e, data) {
					$("#progressReplay").show();
			        var progress = parseInt(data.loaded / data.total * 100, 10);
			        $('#progressReplay .bar').css(
			            'width',
			            progress + '%'
			        );
			    }
		    });
			$('#replayUploadModal').modal('show');
		});
	}
}