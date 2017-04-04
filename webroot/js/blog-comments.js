function addComment(comment)
{
	var container = $('.comments-section');
	var html = '<blockquote>' + comment.content;
	html += '<cite>' + comment.name + ', ' + comment.date + '</cite></blockquote>';
	container.append(html);
}

$(function() {
	// Check if there are comments for this blog
	$.get('/comment/blog/' + blog_id, function(data) {
		$.each(data, function(index, comment) {
			addComment(comment);
		});
	});

	// Link back
	$('.search-back-link').on('click', function() {
		document.frmSearchBack.submit();
	});
});