<?php
$this->assign('title', $blog['title'] ?? 'View Blog');
$this->Html->css('vendor/jquery-ui-1.12.1.custom/jquery-ui.min', ['block' => true]);
$this->Html->script('vendor/jquery-3.2.0.min', ['block' => true]);
$this->Html->script('vendor/jquery-ui-1.12.1.custom/jquery-ui.min', ['block' => true]);
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
<div class="row blog-image">
	<div class="large-8 large-offset-2">
		<img src="<?= $blog['image'] ?>">
	</div>
</div>
<div class="row">
	<div class="large-8 large-offset-2">
		<h1><?= $blog['title'] ?></h1>
		<small>Published on <strong><?= $blog['date'] ?></strong> by <strong><?= $this->Html->link($blog['author']['name'], $blog['author']['href'], ['target' => '_blank']) ?></strong></small>
		<br>
		<small><a href="#" class="search-back-link">&laquo; Back to Search</a> | <?= $this->Html->link('Edit this post', ['controller' => 'Blog', 'action' => 'edit', $blog['id']]) ?> | PDF | Share | Tweet </small>
		<hr>
		<?= $blog['content'] ?>
		<hr>
	</div>
</div>
<div class="row">
	<div class="large-8 large-offset-2 comments-section">
		<h4>User Comments</h4>
	</div>
</div>