<?php
$this->Html->css('blog', ['block' => true]);
$this->Html->script(['vendor/ckeditor/ckeditor.js'], ['block' => true]);
$this->Html->script('blog-edit', ['block' => true]);
?>
<form name="frmBlogViewBack" method="post" action="/blog/<?= $id ?>">
    <div style="display:none;">
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="sb" value="<?= $search_data['sb'] ?? '' ?>">
        <input type="hidden" name="sd" value="<?= $search_data['sd'] ?? '' ?>">
        <input type="hidden" name="published_date" value="<?= $search_data['published_date'] ?? '' ?>">
        <input type="hidden" name="search" value="<?= $search_data['search'] ?? '' ?>">
        <input type="hidden" name="user" value="<?= $search_data['user'] ?? '' ?>">
    </div>
</form>
<form name="frmDeleteBlog" action="/blog/delete/<?= $id ?>" method="post">
	<div style="display:none;">
        <input type="hidden" name="_method" value="DELETE">
    </div>
</form>
<form name="frmEditBlog" action="/blog/edit/<?= $id ?>" method="post">
	<div style="display:none;">
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="e_edit" value="">
        <input type="hidden" name="e_title" value="">
        <input type="hidden" name="e_content" value="">
    </div>
	<?= $this->element('Blog/blog-content', ['mode' => 'edit']) ?>
</form>