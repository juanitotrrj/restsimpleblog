<?php
if (in_array($mode, ['edit']))
{
	$this->assign('title', $blog['title'] ?? 'Edit Blog');
	$this->Html->script(['blog-edit'], ['block' => true]);
}

$this->Html->css('blog', ['block' => true]);
?>
<div class="row blog-image">
<?php if (in_array($mode, ['edit', 'create'])): ?>
	<div class="large-2 columns edit-nav">
		<u><h5>Actions</h5></u>
		<button type="button" id="btn-edit-save" class="success button">Save</button>
		<br>
		<button type="button" id="btn-edit-cancel" class="secondary button">Cancel</button>
	<?php if (in_array($mode, ['edit'])): ?>
		<br>
		<button type="button" id="btn-edit-delete" class="alert button">Delete</button>
	<?php endif; ?>
	</div>
<?php endif; ?>
	<div class="large-offset-2 large-8 columns">
		<div class="row">
		<?php if (in_array($mode, ['create'])): ?>
			<?= $this->Form->file('image') ?>
		<?php else: ?>
			<img src="<?= $blog['image'] ?>">
		<?php endif; ?>
		</div>
		<div class="row">
			<div class="large-12">
				<hr>
				<div id="blogtitle" contenteditable="<?= (in_array($mode, ['edit', 'create'])) ? 'true' : 'false' ?>"><h1><?= $blog['title'] ?></h1></div>
			<?php if (in_array($mode, ['show'])): ?>
				<small>Published on <strong><?= $blog['date'] ?></strong> by <strong><?= $this->Html->link($blog['author']['name'], $blog['author']['href'], ['target' => '_blank']) ?></strong></small>
				<br>
				<small>Last updated on <strong><?= $blog['date_updated'] ?></strong></small>
				<br>
				<small>Tags: 
				<?php foreach ($blog['tags'] as $tag): ?>
					<span class="label secondary"><small><?= $tag ?></small></span>&nbsp;
				<?php endforeach; ?>
				</small>
				<br>
				<small>Categories: 
				<?php foreach ($blog['categories'] as $category): ?>
					<span class="label secondary"><small><?= $category ?></small></span>&nbsp;
				<?php endforeach; ?>
				</small>
				<br>
			<?php endif; ?>
			<?php if (in_array($mode, ['download', 'show'])): ?>
				<?= $this->element('Blog/blog-nav') ?>
			<?php endif; ?>
				<hr>
				<div id="blogcontent" contenteditable="<?= (in_array($mode, ['edit', 'create'])) ? 'true' : 'false' ?>">
					<?= $blog['content'] ?>
				</div>
				<hr>
			</div>
		</div>
	</div>
	<div class="large-2 columns edit-nav fix-right">
<?php if (in_array($mode, ['edit'])): ?>
		<u><h5>Revision History</h5></u>
		<dd>
		<?php foreach ($blog['revisions'] as $revision): ?>
			<blockquote>
				<small><?= $revision['date'] ?></small>
			</blockquote>
		<?php endforeach; ?>
		</dd>
<?php else: ?>
		&nbsp;
<?php endif; ?>
	</div>
</div>