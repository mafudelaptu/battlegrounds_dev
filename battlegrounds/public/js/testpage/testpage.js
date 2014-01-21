$(function () {
	if (document.URL.indexOf("/testpage") >= 0) {
		l("ada");
		$('#fileupload').fileupload({
			dataType: 'json',
			done: function (e, data) {
				l(data.result);
				$.each(data.result.files, function (index, file) {
					$('<p/>').text(file.name).appendTo(document.body);
				});
			}
		});
		l("asdadadssada");
	}

});