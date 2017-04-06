<link href="/css/main.css" rel="stylesheet">
<?php
$path = $blog['image'];
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>
<img src="<?= $base64 ?>">
<br><br>
<h1><?= strip_tags($blog['title']) ?></h1>
<p><?= strip_tags($blog['content']) ?></p>