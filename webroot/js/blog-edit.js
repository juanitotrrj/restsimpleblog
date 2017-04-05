$(function() {
	vex.dialog.alert('Click the title, image, and/or content to show the rich text editors.');

	// Save
	$('#btn-edit-save').on('click', function(e) {
		document.frmEditBlog.e_edit.value = true;
		document.frmEditBlog.e_title.value = CKEDITOR.instances.blogtitle.getData();
		document.frmEditBlog.e_content.value = CKEDITOR.instances.blogcontent.getData();
		document.frmEditBlog.submit();
	});

	// Cancel
	$('#btn-edit-cancel').on('click', function(e) {
		document.frmBlogViewBack.submit();
	});

	// Delete
	$('#btn-edit-delete').on('click', function(e) {
		document.frmDeleteBlog.submit();
	});
});