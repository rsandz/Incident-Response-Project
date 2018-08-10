<body>
	<section class="hero is-dark is-fullheight is-bold">
		<div class="hero-head">
		</div>
		<div class="hero-body">
			<div class="container has-text-centered">
				<img src="<?php echo assets_url('img/logo.png')?>" alt="The Incident Response Project">
				<div class="section">
					<div class="field is-grouped is-grouped-centered">
						<div class="control">
							<?php echo anchor('login', 'Login', 'class="button is-info is-large"'); ?>
						</div>
						<div class="control">
							<?php echo anchor('https://github.com/rsandz/step_project/wiki', 'Wiki', 'class="button is-primary is-large"'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="hero-foot">
			<div class="level">
				<div class="level-left"></div>
				<div class="level-right">
					<div class="level-item">
						<p>Found an issue? Report it <a href="https://github.com/rsandz/step_project/issues" class='has-text-link'>here</a>.</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<hr>