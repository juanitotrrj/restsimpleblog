<?php
$this->assign('title', $blog['title'] ?? 'View Blog');
$this->Html->css('blog', ['block' => true]);
?>
<?= $this->element('Blog/blog-content', ['nav' => true, 'edit' => false, 'mode' => 'download']) ?>