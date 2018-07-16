<?php
	// Setting Defaults
	$header['colour'] = isset($header['colour']) ? $header['colour'] : 'is-info';
	$header['text'] = isset($header['text']) ? $header['text'] : $title;
?>

<body>
	<div class="hero <?=$header['colour']?>">
		<div class="hero-head">
			<div class="level">
				<div class="level-left"></div>
				<div class="level-right">
					<div class="level-item">
						<p>
							Need Help? <a href="" class="">Click Here</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="hero-body">
			<h1 class="title"><?=$header['text']?></h1>
		</div>
	</div>
</body>
</html>