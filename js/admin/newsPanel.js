$(document).ready(function() {
	l(document.URL);
	if (document.URL.indexOf("/newsPanel.php") >= 0) {
		$('.datepicker').datepicker();
	}
});

function nPCreateNewNews() {
	var title = $("#nPTitle").val();
	var content = $("#nPContent").val();
	var order = $("#nPOrder").val();
	var showDate = $("#nPShowDate").val();
	var endDate = $("#nPEndDate").val();

	var activeChecked = $("#nPActive").attr("checked");

	if (activeChecked == "checked") {
		var active = 1;
	}
	else {
		var active = 0;
	}
	l("Start nPCreateNewNews");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "news",
			mode : "createNewNews",
			title : title,
			content : content,
			order : order,
			showDate : showDate,
			endDate : endDate,
			active : active,
		},
		success : function(result) {
			l("nPCreateNewNews success");
			l(result);
			if (result.status !== true) {
				text = "went something wrong @ creating a new News! - Error:" + result.status + "";
			}
			else {
				text = "successfully created a new News!";
			}
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}
	});
	l("End nPCreateNewNews");
}

function nPEditNews(id) {
	l("Start nPEditNews");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "news",
			mode : "editNews",
			id : id
		},
		success : function(result) {
			l("nPEditNews success");
			l(result);
			if (result.status !== true) {
					
			}
			else {
				$("#nPEModalBody").html(result.data);
				$("#nPEditNewsSaveChanges").attr("onclick", "nPEUpdateNews("+result.newsData.NewsID+")");
				$("#nPEditNewsModal").modal({
					backdrop : "static",
					keyboard : false
				}).css({
					width : '81%',
					'margin-left' : function() {
						return -($(this).width() / 2);
					}
				});
				$('.datepicker').datepicker();
				$(".datepicker").css("z-index", "1151");
			}
		}
	});
	l("End nPEditNews");
}

function nPDeleteNews(id) {
	l("Start nPDeleteNews");

	bootbox.confirm("Are you sure that you want to delete that News?", function(result) {
		if (result) {
			$.ajax({
				url : '../ajaxAdmin.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "news",
					mode : "deleteNews",
					id : id,
				},
				success : function(result) {
					l("nPDeleteNews success");
					l(result);
					if (result.status !== true) {
						text = "went something wrong @ deleting News! - Error:" + result.status + "";
					}
					else {
						text = "successfully deleted news!";
					}
					bootbox.alert(text, function() {
						window.location.reload();
					});
				}
			});
			l("End nPDeleteNews");
		}
	});
}

function nPToggleActiveNews(id, active) {
	l("Start nPToggleActiveNews");
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "news",
			mode : "toggleActiveNews",
			id : id,
			active : active
		},
		success : function(result) {
			l("nPToggleActiveNews success");
			l(result);
			if (result.status !== true) {
				text = "went something wrong @ updating News! - Error:" + result.status + "";
			}
			else {
				text = "successfully updated!";
			}
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}
	});
	l("End nPToggleActiveNews");
}

function nPEUpdateNews(id){
	var title = $("#nPETitle").val();
	var content = $("#nPEContent").val();
	var order = $("#nPEOrder").val();
	var showDate = $("#nPEShowDate").val();
	var endDate = $("#nPEEndDate").val();

	var activeChecked = $("#nPEActive").attr("checked");

	if (activeChecked == "checked") {
		var active = 1;
	}
	else {
		var active = 0;
	}
	
	l("Start nPEUpdateNews");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "news",
			mode : "updateNews",
			id: id,
			title : title,
			content : content,
			order : order,
			showDate : showDate,
			endDate : endDate,
			active : active,
		},
		success : function(result) {
			l("nPEUpdateNews success");
			l(result);
			if (result.status !== true) {
				text = "went something wrong @ updating the News! - Error:" + result.status + "";
			}
			else {
				text = "successfully updated News!";
			}
			
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}
	});
	l("End nPEUpdateNews");
}