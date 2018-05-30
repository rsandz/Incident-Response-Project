<nav class="navbar is-dark">
	<div class="navbar-brand">
		<div class="navbar-burger"></div>
	</div>
	<div class="navbar-menu is-active">
		<div class="navbar-start">
			<?php echo anchor('home', 'Dashboard', 'class="navbar-item"'); ?>
			<?php echo anchor('logging', 'Logging Form', 'class="navbar-item"'); ?>
			<?php echo anchor('create', 'Create', 'class="navbar-item"'); ?>
			<?php echo anchor('search', 'Search', 'class="navbar-item"'); ?>
			
		</div>
		<div class="navbar-end">
			<?php echo anchor('logout', 'Logout', 'class="navbar-item"'); ?>
			<?php if ($this->session->privileges == 'admin') echo anchor('admin', 'Admin', 'class="navbar-item"');?>

		</div>
	</div>
</nav>
