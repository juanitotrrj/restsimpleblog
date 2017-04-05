<?php
if ($edit)
{
	$this->assign('title', $blog['title'] ?? 'Edit Blog');
	$this->Html->script(['blog-edit'], ['block' => true]);
}

$this->Html->css('blog', ['block' => true]);
?>
<div class="row blog-image">
<?php if ($edit): ?>
	<div class="large-2 columns edit-nav">
		<button type="button" id="btn-edit-save" class="success button">Save</button>
		<button type="button" id="btn-edit-cancel" class="secondary button">Cancel</button>
		<button type="button" id="btn-edit-delete" class="alert button">Delete</button>
	</div>
<?php endif; ?>
	<div class="large-offset-2 large-8 columns">
		<div class="row">
			<img src="<?= $blog['image'] ?>">
		</div>
		<div class="row">
			<div class="large-12">
				<div id="blogtitle" contenteditable="<?= ($edit) ? 'true' : 'false' ?>"><h1><?= $blog['title'] ?></h1></div>
			<?php if (!$edit): ?>
				<small>Published on <strong><?= $blog['date'] ?></strong> by <strong><?= $this->Html->link($blog['author']['name'], $blog['author']['href'], ['target' => '_blank']) ?></strong></small>
				<br>
				<small>Last updated on <strong><?= $blog['date_updated'] ?></strong></small>
				<br>
			<?php endif; ?>
			<?php if ($nav && (!isset($mode) || $mode !== 'download')): ?>
				<?= $this->element('Blog/blog-nav') ?>
			<?php endif; ?>
				<hr>
				<div id="blogcontent" contenteditable="<?= ($edit) ? 'true' : 'false' ?>">
					<?= $blog['content'] ?>
				</div>
				<hr>
			</div>
		</div>
	</div>
	<div class="large-2">&nbsp;</div>
</div>