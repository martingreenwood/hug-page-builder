$(function() {
	$("#file").fileinput();
});

$(function() {
	$('#summernote').summernote({
		height: 300,                 // set editor height
		minHeight: null,             // set minimum height of editor
		maxHeight: null,             // set maximum height of editor

		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
		    ['font', ['strikethrough', 'superscript', 'subscript']],
		    ['fontsize', ['fontsize']],
		   	['color', ['color']],
	    	['para', ['ul', 'ol', 'paragraph']],
		    ['height', ['height']],
		]
	});
});
