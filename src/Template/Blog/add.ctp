<?php
$this->Html->css(['blog', '/js/vendor/LoadImg/loadimg'], ['block' => true]);
$this->Html->script(['vendor/ckeditor/ckeditor.js', 'blog-add', 'vendor/LoadImg/loadimg'], ['block' => true]);
?>
<?= $this->Form->create(null, ['url' => ['controller' => 'Search', 'action' => 'index'], 'name' => 'frmSearchBack']) ?>
	<div style="display:none;">
		<?= $this->Form->hidden('sb', ['value' => $search_data['sb']]) ?>
		<?= $this->Form->hidden('sd', ['value' => $search_data['sd']]) ?>
		<?= $this->Form->hidden('published_date', ['value' => $search_data['published_date']]) ?>
		<?= $this->Form->hidden('search', ['value' => $search_data['search']]) ?>
		<?= $this->Form->hidden('user', ['value' => $search_data['user']]) ?>
    </div>
<?= $this->Form->end() ?>
<?= $this->Form->create(null, ['url' => ['controller' => 'Blog', 'action' => 'add'], 'type' => 'file', 'name' => 'frmCreatePost']) ?>
	<?= $this->Form->hidden('a_create', ['value' => false]) ?>
	<?= $this->Form->hidden('a_title') ?>
	<?= $this->Form->hidden('a_content') ?>
	<?= $this->element('Blog/blog-content', ['mode' => 'create']) ?>
<?= $this->Form->end() ?>