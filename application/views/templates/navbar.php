<nav class="navbar is-dark">
	<div class="navbar-brand">
		<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
		  <span aria-hidden="true" class="has-background-white"></span>
		  <span aria-hidden="true" class="has-background-white"></span>
		  <span aria-hidden="true" class="has-background-white"></span>
		</a>
	</div>
	<div class="navbar-menu">
		<div class="navbar-start">
			<?php echo anchor('home', 'Dashboard', 'class="navbar-item"'); ?>
			<?php echo anchor('logging', 'Logging Form', 'class="navbar-item"'); ?>
			<?php echo anchor('create', 'Create', 'class="navbar-item"'); ?>
			<?php echo anchor('modify', 'Modify', 'class="navbar-item"'); ?>
			<?php echo anchor('stats', 'Statistics', 'class="navbar-item"'); ?>
			
		</div>
		<div class="navbar-end">
			<?php echo anchor('search', 'Search', 'class="navbar-item"'); ?>
			<?php echo anchor('logout', 'Logout', 'class="navbar-item"'); ?>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo base_url('js/menu.js')?>"></script>
</nav>

