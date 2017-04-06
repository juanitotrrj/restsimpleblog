$(function() {
	vex.dialog.alert('Click the `Blog Title` and `Blog Content` to edit.');

	// Save
	var btn_save = $('#btn-edit-save');
	btn_save.on('click', function(e) {
		document.frmCreatePost.a_create.value = true;
		document.frmCreatePost.a_title.value = CKEDITOR.instances.blogtitle.getData();
		document.frmCreatePost.a_content.value = CKEDITOR.instances.blogcontent.getData();
		document.frmCreatePost.submit();
	});

	// Cancel
	$('#btn-edit-cancel').on('click', function(e) {
		$(document.frmCreatePost).prop('disabled', true);
		btn_save.prop('disabled', true);
		document.frmSearchBack.submit();
	});
});