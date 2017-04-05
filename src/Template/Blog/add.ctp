<?php
$this->Html->css('blog', ['block' => true]);
$this->Html->script(['vendor/ckeditor/ckeditor.js'], ['block' => true]);
$this->Html->script('blog-edit', ['block' => true]);
?>
<?= $this->element('Blog/blog-content', ['mode' => 'create']) ?>