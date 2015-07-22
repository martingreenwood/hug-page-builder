$(function() {
	$("#file").fileinput();
});

$(function() {
	$("ul.pageme").quickPagination({
		pagerLocation:"both",
		pageSize:"10"
	});
});


$(function() {
	$('#summernote').summernote({
		height: 300,                 // set editor height
		minHeight: null,             // set minimum height of editor
		maxHeight: null,             // set maximum height of editor
	});
});
