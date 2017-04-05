function addComment(comment)
{
	var container = $('.comments-section');
	var html = '<blockquote>' + comment.content;
	html += '<cite>' + comment.name + ', ' + comment.date + '</cite></blockquote>';
	container.append(html);
}

function checkComments(callback)
{
	var cs = $('.comments-section');
	var callback = callback || function() {};
	cs.html('<h4>What others are saying</h4>');
	cs.append('<img src="/img/spin.gif" id="comment-loading-gif" style="margin-left: 50%;">');
	$.get('/comment/blog/' + blog_id, function(data) {
		$.each(data, function(index, comment) {
			addComment(comment);
		});

		$('#comment-loading-gif').remove();
		callback();
	});
}

$(function() {
	// Check if there are comments for this blog
	checkComments();

	// Link back
	$('.search-back-link').on('click', function() {
		document.frmSearchBack.submit();
	});

	// Submit comments
	$('#btn-comment-save').on('click', function() {
		var txt_area = $('#text-comment');
		var btn_comment = $(this);
		var txt_comment = txt_area.val();
		
		txt_area.prop('disabled', true);
		btn_comment.prop('disabled', true);
		
		$.post('/comment/add', {m: txt_comment, id: blog_id}, function() {
			checkComments(function() {
				txt_area.prop('disabled', false);
				btn_comment.prop('disabled', false);
				txt_area.val('');
			});
		});
	});
});