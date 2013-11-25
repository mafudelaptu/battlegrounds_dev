/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
var chart1; // globally available
$(document).ready(function() {
	if ($("#profileTabs").length > 0) {

		// zu teams springen wenn in url
		if (document.URL.indexOf("#teams") > 0) {
			$('#profileTabs a[href="#teams"]').tab('show');
		}
		// zu teams springen wenn in url
		if (document.URL.indexOf("#bp") > 0) {
			$('#profileTabs a[href="#bp"]').tab('show');
		}

		$("#searchForPlayerInput").keyup(function() {
			// Result area cleanen
			idResult = "searchForPlayerResults";
			$("#" + idResult).html("<img src='img/ajax-loader.gif'>");

			query = $(this).val();

			clearTimeout(timer);

			timer = setTimeout(function() {

				$.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "user",
						mode : "getAllUser",
						alsoSelf : false,
						query : query,
						typeahead : false,
						justYourLeague : true
					},
					success : function(result) {
						l(result);
						// Ergebnisse anzeigen
						showResultsAfterKeyUp(idResult, result.data);

						$(".t").tooltip();
					}
				});
			}, 400);
		});
	}

	if ($("#generalWinRateTrendChart").length > 0) {
		drawGeneralWinRateTrendChart();
	}

	// ELO-Rose
	if ($("#eloRoseChart").length > 0) {
		drawEloRoseChart();

	}
	// Elo-History
	if ($("#eloHistoryChart").length > 0) {
		drawEloHistoryChart();
	}

});

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function drawGeneralWinRateTrendChart() {
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchDetails",
			mode : "getGeneralWinRateTrendData",
		},
		success : function(result) {
			l(result);
			if (result.data != null) {
				chart1 = new Highcharts.Chart({
					chart : {
						renderTo : 'generalWinRateTrendChart',
						type : 'spline',
						height : 224
					},
					title : {
						text : ''
					},
					yAxis : {
						min : Array.min(result.data),
						max : Array.max(result.data),
						title : {
							text : '',
							margin : 0
						},
						labels : {
							enabled : true,
							align : 'left',
							x : 0,
							y : -2
						},
						plotBands : [ { // Lose-area
							from : 0,
							to : 50,
							color : 'rgba(185, 74, 72, 0.1)',
							label : {
								text : 'more losses',
								style : {
									color : '#606060'
								}
							}
						}, { // Win-area
							from : 50,
							to : 100,
							color : 'rgba(70, 136, 71, 0.1)',
							label : {
								text : 'more wins',
								style : {
									color : '#606060'
								}
							}

						} ]
					},
					xAxis : {
						title : {
							text : ''
						},
						categories : result.xAxis,
						labels : {
							rotation : -45,
							align : 'right',
							enabled : true
						}
					},
					tooltip : {
						formatter : function() {
							return '<b>' + this.series.name + '</b><br/>Match #' + this.x + ': ' + this.y + '%';
						}
					},
					legend : {
						align : "center",
						verticalAlign : 'top',
						enabled : true,
						itemMarginTop : 0,
						itemMarginBottom : 0,
						padding : 5,
					},

					series : [ {
						name : 'Win Rate Trend',
						data : result.data
					} ],
					credits : {
						enabled : false
					},
				});
			}
			else {
				html = '<div class="alert fade in"><p>You have to play at least one match to view this Chart!</p>' + '<img src="img/profile/preview/previewWinRateTrend.png">' + '</div>';
				$("#generalWinRateTrendChart").html(html);

				// Full Link ausblenden
				$("#generalWinRateTrendFullChartLink").hide();

			}

		}
	});
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function drawEloHistoryChart() {

	matchModeID = $("#eloHistoryMMSelect option:selected").val();
	matchTypeID = $("#eloHistoryMTSelect option:selected").val();
	counts = $("#eloHistoryCountSelect option:selected").val();

	var steamID = getParameterByName("ID");

	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "userPoints",
			mode : "getPointsHistoryData",
			matchmode : matchModeID,
			matchtype : matchTypeID,
			count : counts,
			ID : steamID
		},
		success : function(result) {
			l(result);
			if (result.data != null) {
				chart = new Highcharts.Chart({
					chart : {
						renderTo : 'eloHistoryChart'
					},
					title : {
						text : ''
					},
					xAxis : {
						categories : result.xAxis,
						labels : {
							rotation : -45,
							align : 'right',
							enabled : true
						}
					},
					yAxis : {
						title : {
							text : 'Points'
						},
						plotBands : [ { // BRONZE
							from : 0,
							to : result.silverBorder,
							color : 'rgba(205, 133, 63, 0.1)',
							label : {
								text : 'Bronze Ranking',
								style : {
									color : '#606060'
								}
							}
						}, { // SILVER
							from : result.silverBorder,
							to : result.goldBorder,
							color : 'rgba(192, 192, 192, 0.1)',
							label : {
								text : 'Silver Ranking',
								style : {
									color : '#606060'
								}
							}

						}, { // GOLD
							from : result.goldBorder,
							to : result.diamondBorder,
							color : 'rgba(255, 215, 0, 0.1)',
							label : {
								text : 'Gold Ranking',
								style : {
									color : '#606060'
								}
							}

						}, { // DIAMOND
							from : result.diamondBorder,
							to : 99999,
							color : 'rgba(0, 136, 204, 0.1)',
							label : {
								text : 'Diamond Ranking',
								style : {
									color : '#606060'
								}
							}

						} ]
					},
					tooltip : {
						formatter : function() {
							id = this.series.data.indexOf(this.point);
							return generateInfoForPointshistory(result.pointsType[id], result.pointsChange[id], this.y, result.idText[id]);
						}
					},

					legend : {
						enabled : false
					},
					series : [ {
						type : "line",
						name : "Point-History for " + result.matchType + ": " + result.matchMode,
						data : result.data
					// data : [ 1850, 1620, 1500, 1333, 1100, 1060, 900, 1570 ]
					} ],
					credits : {
						enabled : false
					},
				});
			}
			else {
				html = '<div class="alert fade in"><p>You have to play at least one match in this Matchmode to view this Chart!</p>' + '<img src="img/profile/preview/previewEloHistory.png">'
						+ '</div>';
				$("#eloHistoryChart").html(html);

			}

		}
	});
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function drawEloRoseChart() {

	var steamID = getParameterByName("ID");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "userPoints",
			mode : "getPointRoseData",
			sID : steamID
		},
		success : function(result) {
			l(result);
			l($(result.keys).size());
			if ($(result.keys).size() > 2) {
				series = [ {
					type : "area",
					name : 'Point-Area',
					data : result.data,
					pointPlacement : 'on'
				} ];

				maxValue = Array.max(result.data);
				// if (Array.max(result.data) > result.diamond[1]) {
				// series[1] = {
				// type : "line",
				// name : 'Diamond Border',
				// data : result.diamond,
				// pointPlacement : 'on',
				// visible : false
				// };
				// series[2] = {
				// type : "line",
				// name : 'Gold Border',
				// data : result.gold,
				// pointPlacement : 'on',
				// visible : false
				// };
				//
				// series[3] = {
				// type : "line",
				// name : 'Silver Border',
				// data : result.silver,
				// pointPlacement : 'on',
				// visible : false
				// };
				// maxValue = Array.max(result.data);
				// } else if (Array.max(result.data) >= result.gold[1]
				// && Array.max(result.data) < result.diamond[1]) {
				// series[3] = {
				// type : "line",
				// name : 'Silver Border',
				// data : result.silver,
				// pointPlacement : 'on',
				// visible : false
				// };
				// series[2] = {
				// type : "line",
				// name : 'Gold Border',
				// data : result.gold,
				// pointPlacement : 'on',
				// visible : false
				// };
				// series[1] = {
				// type : "line",
				// name : 'Diamond Border',
				// data : result.diamond,
				// pointPlacement : 'on',
				// visible : false
				// };
				// maxValue = result.diamond[1];
				// } else if (Array.max(result.data) >= result.silver[1]
				// && Array.max(result.data) < result.gold[1]) {
				// series[3] = {
				// type : "line",
				// name : 'Silver Border',
				// data : result.silver,
				// pointPlacement : 'on',
				// visible : false
				// };
				// series[2] = {
				// type : "line",
				// name : 'Gold Border',
				// data : result.gold,
				// pointPlacement : 'on',
				// visible : false
				// };
				// series[1] = {
				// type : "line",
				// name : 'Diamond Border',
				// data : result.diamond,
				// pointPlacement : 'on',
				// visible : false
				// };
				// maxValue = result.gold[1];
				// } else {
				// series[3] = {
				// type : "line",
				// name : 'Silver Border',
				// data : result.silver,
				// pointPlacement : 'on',
				// visible : false
				// };
				// maxValue = result.silver[1];
				// }
				maxValue = Array.max(result.data);
				l(maxValue);
				chart1 = new Highcharts.Chart({
					chart : {
						renderTo : 'eloRoseChart',
						polar : true,
						type : 'area'
					},

					title : {
						text : '',
					// x: -80
					},

					xAxis : {
						categories : result.keys,
						tickmarkPlacement : 'on',
						lineWidth : 0,
					},
					colors : [ '#AA4643', '#08C', 'gold', 'silver', '#3D96AE', '#DB843D', '#92A8CD', '#A47D7C', '#B5CA92' ],
					yAxis : {
						gridLineInterpolation : 'polygon',
						lineWidth : 0,
						min : (Array.min(result.data)),
						max : maxValue
					},

					tooltip : {
						shared : true,
						valuePrefix : ''
					},

					legend : {
						align : "center",
						verticalAlign : 'top',
						enabled : true,
					},

					series : series,

					credits : {
						enabled : false
					},
				});
			}
			else {
				html = '<div class="alert fade in"><p>You have to play at least one match in more than 2 Matchmodes to view this Chart!</p>' + '<img src="img/profile/preview/previewEloRose.png">'
						+ '</div>';
				$("#eloRoseChart").html(html);
			}

		}
	});
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function showResultsAfterKeyUp(id, data) {
	l("Start showResultsAfterKeyUp");
	$("#" + id).html("");
	if (data != null || data == false || data.length == 0) {
		var i = 0;
		l(data);
		l(id);

		$.each(data, function(index, value) {
			var html = "";
			l(value);
			if ((i % 3) == 0) {
				html += "<div class='clearer'></div>";
			}
			l(value.Name.length);
			if (value.Name.length > 15) {
				var name = value.Name.substring(0, 15) + "...";
				var classTipsy = "t";
				var titleText = "data-original-title='" + value.Name + "'";
			}
			else {
				var name = value.Name;
				var classTipsy = "";
				var titleText = "";
			}

			html += "<div class='pull-left' style='width:33%'><label class='radio'><input type='radio' name='createGroupSelection' value='" + value.SteamID + "' id='" + value.SteamID + "'>&nbsp;"
					+ "<img src='" + value.Avatar + "'><span class=" + classTipsy + " " + titleText + ">" + name + "</span></label></div>";
			$("#" + id).append(html);
			i++;
		});
	}
	else {
		html = "<div class='alert alert-warning'>No matching data found...</div>";
		$("#" + id).html(html);
	}

	l("End showResultsAfterKeyUp");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function generateInfoForPointshistory(pointsType, pointsChange, totalPoints, idText) {
	ret = "";
	if (parseInt(pointsChange) > 0) {
		pointsChange = "+" + pointsChange;
	}
	ret += "<b>" + idText + " - " + pointsType + ": " + pointsChange + "</b><br>";
	ret += "Points: " + totalPoints;
	return ret;
	// return '<b>' + this.series.name + '</b><br/>Match #'
	// + this.x + ': ' + this.y + " Points";

}

function profileChangeSkillBracketIcon(switchTo) {
	var idSingle5vs5 = "single5vs5SkillBracketIcon";
	var id1vs1 = "1vs1SkillBracketIcon";
	var idTeam5vs5 = "team5vs5SkillBracketIcon";
	l(switchTo);
	switch (switchTo) {
		case "single5vs5":
			l(switchTo);
				// show
				$("#"+idSingle5vs5).removeClass("hide");
				// hide others
				$("#"+id1vs1).addClass("hide");
				$("#"+idTeam5vs5).addClass("hide");
			break;
		case "1vs1":
			l(switchTo);
			// show
			$("#"+id1vs1).removeClass("hide");
			// hide others
			$("#"+idSingle5vs5).addClass("hide");
			$("#"+idTeam5vs5).addClass("hide");
			break;
		case "team5vs5":
			l(switchTo);
			// show
			$("#"+idTeam5vs5).removeClass("hide");
			// hide others
			$("#"+id1vs1).addClass("hide");
			$("#"+idSingle5vs5).addClass("hide");
			break;
	}
}
