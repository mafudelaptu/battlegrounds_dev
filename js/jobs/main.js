/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document)
		.ready(
				function() {
					l(document.URL);
					if (url.indexOf("/jobs.php") >= 0) {

						$("#jobsForm")
								.validate(
										{
											rules : {
												inputEmail: {
													required: true,
													email : true
												},
												inputName : "required",
												inputCountry : "required",
												inputAge : "required",
												inputLvlEdu : "required",
												inputBGEsports : "required",
												inputProjects : "required",
												inputGoodAt : "required",
												inputContribute : "required",
											}
										});

					}
				});