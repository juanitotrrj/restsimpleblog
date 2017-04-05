<?php
$this->assign('title', $blog['title'] ?? 'View Blog');
$this->Html->script(['blog', 'blog-comments'], ['block' => true]);
$this->Html->css('blog', ['block' => true]);
$this->Html->scriptStart(['block' => true]);
?>
	var blog_id = <?= $blog['id'] ?>;
<?php $this->Html->scriptEnd(); ?>
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
<?= $this->element('Blog/blog-content', ['nav' => true, 'edit' => false]) ?>
<div class="row">
	<div class="large-8 large-offset-2 comments-section">
		<h4>User Comments</h4>
	</div>
</div>