<body>
	<div class="hero is-info">
		<div class="hero-body">
			<h1 class="title">Hello, <?php echo $name; ?>. Welcome to your Dashboard</h1>
		</div>
	</div>
	<div class="column is-half is-offset-one-quarter">
		<div class="box has-text-centered">
			<div class="field">
				<?php echo anchor('logging', 'Logging Form', 'class="button is-link"'); ?>
			</div>
			<div class="field">
				<?php if ($privileges == 'admin') echo anchor('admin', 'Admin', 'class="button is-link"'); ?>
			</div>
			<div class="field">
				<?php echo anchor('logout', 'Logout', 'class="button is-link"'); ?>
			</div>
		</div>
	</div>

</body>
</html>