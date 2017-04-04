<?php
$this->Html->script(['vendor/jquery-3.2.0.min', 'vendor/ckeditor/ckeditor.js', 'blog-edit'], ['block' => true]);
?>
<form name="frmSearchBack" method="post" action="/search">
    <div style="display:none;">
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="sb" value="<?= $search_data['sb'] ?? '' ?>">
        <input type="hidden" name="sd" value="<?= $search_data['sd'] ?? '' ?>">
        <input type="hidden" name="published_date" value="<?= $search_data['published_date'] ?? '' ?>">
        <input type="hidden" name="search" value="<?= $search_data['search'] ?? '' ?>">
        <input type="hidden" name="user" value="<?= $search_data['user'] ?? '' ?>">
    </div>
</form>
<form name="frmEditBlog" action="/blog/edit/<?= $id ?>" method="post">
	<div class="row">
		<div class="large-offset-2 large-8">
			<h1>Edit Blog</h1>
			<input type="text" placeholder="title" value="<?= $blog['title'] ?>">
		</div>
	</div>
	<div class="row">
		<div class="large-offset-2 large-8">
			<textarea id="blogcontent" name="blogcontent">
				<?= $blog['content'] ?>
			</textarea>
			<script>
				CKEDITOR.replace('blogcontent');
			</script>
			<hr>
		</div>
	</div>
	<div class="row">
		<div class="large-offset-2 large-8">
			<button>Save</button>
			<button>Cancel</button>
			<button>Preview</button>
		</div>
	</div>
</form>