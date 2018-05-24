<?php
	// Setting Defaults
	$header['colour'] = isset($header['colour']) ? $header['colour'] : 'is-info';
	$header['text'] = isset($header['text']) ? $header['text'] : 'Title';
?>

<body>
	<div class="hero <?=$header['colour']?>">
		<div class="hero-body">
			<h1 class="title"><?=$header['text']?></h1>
		</div>
	</div>
</body>
</html>