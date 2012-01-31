<?php 
header("Content-Type: text/xml; charset=utf8");
echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<st1234reply>
	<requesttime><?= $_SERVER['REQUEST_TIME'] ?></requesttime>
	<requestmethod><?= $_SERVER['REQUEST_METHOD'] ?></requestmethod>
	<replytime><?= time() ?></replytime>	
	<?php foreach ($tags as $tag) echo $tag; ?>	
</st1234reply> 