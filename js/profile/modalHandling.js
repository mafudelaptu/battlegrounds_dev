/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function showFullGeneralWinRateTrend() {
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchDetails",
			mode : "getFullGeneralWinRateTrendData",
		},
		success : function(result) {
			l(result.data);
			// Modal öffnen
			
			$('#myModalfullGeneralWinRate').modal({
				backdrop : true,
				keyboard : true
			}).css({
				width : '81%',
				'margin-left' : function() {
					return -($(this).width() / 2);
				}
			});
			
			chart1 = new Highcharts.Chart({
				chart : {
					renderTo : 'generalFullWinRateTrendChart',
					type : 'spline'
				},
				title : {
					text : ''
				},
				yAxis : {
					min : Array.min(result.data),
					max : Array.max(result.data),
					title : {
						text : 'Win Rate Trend (%)'
					},
		            labels: {
		                enabled: true
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
						text : 'Match (#)'
					},
					categories: result.xAxis,
					labels : {
						enabled : false
					}
				},
				tooltip : {
					formatter : function() {
						return '<b>' + this.series.name
						+ '</b><br/>Match #'+this.x+': ' + this.y + '%';
					}
				},
				legend : {
					enabled : true
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
	});
}