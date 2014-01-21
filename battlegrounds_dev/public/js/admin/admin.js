$(function(){
	if (document.URL.indexOf("/admin") >= 0) {
		initPanelLinks();
	}
});

function initPanelLinks(){
	var links = $("#adminPanelLinks .adminPanelLink");
	l(links);
	links.click(function(){
		href = $(this).attr("data-link");
		window.location = ARENA_PATH+"/admin/"+href;
	});
}