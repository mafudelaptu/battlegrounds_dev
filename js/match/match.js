/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function submitResult(){
	var matchID = getParameterByName("matchID");
	validUpload = checkScreenshotUploaded();
	l("submitResult submit clicked");
	l("upload:"+validUpload);
	

	
	if ($('#submitResultForm').valid() && checkScreenshotUploaded()) {
		
		// Submit auf disabled setzen
		$("#matchSubmitresultButton").html("Result submitted").addClass(
				"disabled").removeClass("btn-primary").removeAttr("onclick");
		// Cancel button removen
		$("#cancelMatchButton").remove();
		
		var selection = $(
				"#myModalSubmitResult div[data-toggle='buttons-radio']>button[class*='active']")
				.val();
		
		l(selection);
		
		if(fileUploaded != null){
			fileName = fileUploaded.name;
		}
		else{
			fileName = "";
		}
		
		// LeaverVotes auslesen
		checkedInputs = $("#leaverPannel input[type='checkbox']:checked");
		// array zum uebergeben zusammenbauen
		leaverArray = new Array();
		$.each(checkedInputs, function(index,value){
			leaverArray.push($(value).val());
		});
		
		
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "match",
				mode : "submitResult",
				value : selection,
				ID: matchID,
				screenshot : fileName,
				leaver : leaverArray
			},
			success : function(result) {
				l(result);
				$.cookie("matchFoundResultSubmitted", matchID, {
					expires : 1
				});
				
						
				// match found cookie loeschen
				$.cookie("matchFound", null);
				
				l("SubmitResult success");
				$('#myModalSubmitResult').modal('hide');
				l("SubmitResult End");
				
				// MatchTeams leeren von Spieler
				cleanMatchTeamsOfPlayer();
				
				// Weiterleitung aufs Profil
				setTimeout(function() {
					//checken ob eventMatch jenachdem unterschiedlich umleiten
					statusEvent = checkIfEventMatch();
					l(statusEvent);
					if(statusEvent){
						// auf Profil umleiten
						window.location = "event.php?eventID="+statusEvent[0]+"&cEID="+statusEvent[1];
					}
					else{
						// auf Profil umleiten
						//window.location = "profile.php";
					}
				}, 1000);
				
			}
		});
	}

}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
var fileUploaded = null;
function checkForStrangeSubmissions(){
	l("Start checkForStrangeSubmissions");
	var matchID = getParameterByName("matchID");
	
	$('#submitResultForm').validate({
	    rules: {
//	    	inputDota2MatchID: {
//		        minlength: 7,
//		        maxlength:8,
//		        required: true,
//		        digits: true
//		    },
		    checkWinLoseCheckboxHidden: {
		        minlength: 3,
		        maxlength:4,
		        required: true
		    }
	    },
	    highlight: function(label) {
	    	$(label).closest('.control-group').addClass('error');
	    },
	    success: function(label) {
	    	label
	    		.text('OK!').addClass('valid')
	    		.closest('.control-group').addClass('success');
	    }
	  });

	$('#myModalSubmitResult').modal('show');
	
	$.ajax({
		url: 'ajax.php',
		type: "POST",
		dataType: 'json',
		data: {
			type:"matchDetails", 
			mode: "checkForStrangeSubmissions", 
			ID:matchID
		},
		success: function(result) {
			fileUploaded = null;
			l(result);
			
			if(result.oneVsOne){	
					$("#screenshotUploadForm").show();
					
					$('#screenshotUpload').fileupload({
				        dataType: 'json',
				        done: function (e, data) {
				        	$("#progress").hide();
				        	l(data);
				        	
				            $.each(data.result.files, function (index, file) {
				            	l(file);
				            	$("#fileUploaded").html("<img src='"+file.thumbnail_url+"' height=50>"+file.name);
				            	
				            	
				            });
				            fileUploaded = data.result.files[0];
				            $.ajax({
				        		url: 'ajax.php',
				        		type: "POST",
				        		dataType: 'json',
				        		data: {type:"uploads", mode: "moveScreenshotFile", ID:matchID, fileName: fileUploaded.name},
				        		success: function(result2) {
				        			
				        			l(result2);
				        		}
				        	
				            });
				        },
						progressall: function (e, data) {
							$("#progress").show();
					        var progress = parseInt(data.loaded / data.total * 100, 10);
					        $('#progress .bar').css(
					            'width',
					            progress + '%'
					        );
					    }
				    });
				
			}
			else{
				// leaverCounts hinzuf�gen
				setLeaverCounts(result.leaverData.countArray); // matchModal.js
				
				// screenshot handling
				if(result.screenshot == "screenshot"){
					
					$("#screenshotUploadForm").show();
					
					$('#screenshotUpload').fileupload({
				        dataType: 'json',
				        done: function (e, data) {
				        	$("#progress").hide();
				        	l(data);
				        	
				            $.each(data.result.files, function (index, file) {
				            	l(file);
				            	$("#fileUploaded").html("<img src='"+file.thumbnail_url+"' height=50>"+file.name);
				            	
				            	
				            });
				            fileUploaded = data.result.files[0];
				            $.ajax({
				        		url: 'ajax.php',
				        		type: "POST",
				        		dataType: 'json',
				        		data: {type:"uploads", mode: "moveScreenshotFile", ID:matchID, fileName: fileUploaded.name},
				        		success: function(result2) {
				        			
				        			l(result2);
				        		}
				        	
				            });
				        },
						progressall: function (e, data) {
							$("#progress").show();
					        var progress = parseInt(data.loaded / data.total * 100, 10);
					        $('#progress .bar').css(
					            'width',
					            progress + '%'
					        );
					    }
				    });
				}
			}
			

			// leaver handling
			if(result.leaver == "leaver"){
				// hervorhebung hinzuf�gen
				$("#leaverPannelToggler").addClass("alert alert-warning");
				
				// sofort ausklappen
				$("#leaverPannel").addClass("in");
			}
		}
	});
	
	l("END checkForStrangeSubmissions");
}

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function setWinLoseValue(button){
	l(button, debug);
	var value = $(button).val();
	l(value, debug);
	$("#checkWinLoseCheckboxHidden").val(value);
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function checkScreenshotUploaded(){
	l("Start checkScreenshotUploaded");
	screenshotStatus = $("#screenshotUploadForm").css("display");
	l(screenshotStatus);
	if(screenshotStatus == "block"){
		if(fileUploaded != null){
			l("uploaded");
			$("#screenshotUploadFormError").remove();
			return true;
		}
		else{
			l("error: ");
			l(fileUploaded);
			error = '<label for="screenshotUploadForm" id="screenshotUploadFormError" generated="true" class="error" style="">This field is required.</label>';
			$("#screenshotUploadForm > .controls").append(error);
			
			return false;
		}
	}
	else{
		l("screenshot wird nciht verlangt");
		return true;
	}
	
	l("End checkScreenshotUploaded");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function submitCancelMatch(){
	
	var matchID = getParameterByName("matchID");
	
	// reset Fehlerstatus
	$("#leaverCancelMatchPannel input[type='checkbox']").css("color", "black");
	$("#checkErrorDiv").html("");
	
	// reason auslesen
	reason = $("#checkGroup button.active").val();
	l(reason);
	
	
	// LeaverVotes auslesen
	checkedInputs = $("#leaverCancelMatchPannel input[type='checkbox']:checked");
	// array zum uebergeben zusammenbauen
	leaverArray = new Array();
	$.each(checkedInputs, function(index,value){
		leaverArray.push($(value).val());
	});
	
	switch(reason){
		case "1":
			if(leaverArray.length > 0){
				l("test");
				l(reason);
				$.ajax({
					url: 'ajax.php',
					type: "POST",
					dataType: 'json',
					data: {type:"match", mode: "cancelMatch", ID:matchID, array:leaverArray, reason:reason},
					success: function(result) {
						l(result);
						
						if(result.status == true){
							// MatchTeams leeren von Spieler
							cleanMatchTeamsOfPlayer();
							
							// aktuelles Modal schließen
							$("#myModalCancelMatch").modal("hide");
							
							// neues Modal oeffnen
							$("#myModalCancelMatchSuccess").modal("show");
							
							$("#myModalCancelSuccessBackButton").click(function(){
								// Modal schließen
								$("#myModalCancelMatchSuccess").modal("hide");
								
								// andere Button disabled
								html = '<div class="alert-info">you voted for canceling the Match!</div>';
								$("#middleAreaButtonArea").html(html);
								
							});
							
							$("#myModalCancelMatchSuccessLeaveButton").click(function(){
								// Modal schließen
								$("#myModalCancelMatchSuccess").modal("hide");
								
								// auf Profil umleiten
								window.location = "profile.php";
								
							});
						}
					}
				});
			}
			// Fehlermeldung anzeigen
			else{
				$("#leaverCancelMatchPannel input[type='checkbox']").css("color", "red");
				
				error = '<br><div class="alert alert-block alert-error"><p>select at least one Player who didn\'t join the Match!</p></div>';
				
				$("#checkErrorDiv").html(error);
			}
			break;
		case "2":
			$.ajax({
				url: 'ajax.php',
				type: "POST",
				dataType: 'json',
				data: {type:"match", mode: "cancelMatchHard", ID:matchID},
				success: function(result) {
					l(result);
					
					if(result.status == true){
						// MatchTeams leeren von Spieler
						cleanMatchTeamsOfPlayer();
						
						// aktuelles Modal schließen
						$("#myModalCancelMatch").modal("hide");
						
						// neues Modal oeffnen
						$("#myModalCancelHardMatchSuccess").modal("show");
						
						$("#myModalCancelHardSuccessBackButton").click(function(){
							// Modal schließen
							$("#myModalCancelMatchSuccess").modal("hide");
							
							// andere Button disabled
							html = '<div class="alert-info">you voted for canceling the Match!</div>';
							$("#middleAreaButtonArea").html(html);
							
						});
						
						$("#myModalCancelHardMatchSuccessLeaveButton").click(function(){
							// Modal schließen
							$("#myModalCancelMatchSuccess").modal("hide");
							
							//checken ob eventMatch jenachdem unterschiedlich umleiten
							statusEvent = checkIfEventMatch();
							if(statusEvent){
								// auf Profil umleiten
								window.location = "event.php?eventID="+statusEvent[0]+"&cEID="+statusEvent[1];
							}
							else{
								// auf Profil umleiten
								window.location = "profile.php";
							}
							
						});
					}
				}
			});
			break;
	}

}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function checkIfEventMatch(){
	ret = new Array();
	
	id = "#eventMatchIndicator";
	eventMatch = $(id).val();
	if(eventMatch == "true"){
		createdEventID = $(id).attr("data-CEID");
		eventID = $(id).attr("data-EID");
		l(createdEventID+" "+eventID);
		
		ret.push(eventID);
		ret.push(createdEventID);
		
	}
	else{
		ret = false;
	}
	
	return ret;
}
