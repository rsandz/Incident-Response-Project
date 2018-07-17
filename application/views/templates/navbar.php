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
			<?php echo anchor('Dashboard', 'Dashboard', 'class="navbar-item"'); ?>
			<?php echo anchor('Logging', 'Log an Activity', 'class="navbar-item"'); ?>
			<?php echo anchor('Create', 'Create', 'class="navbar-item"'); ?>
			<?php echo anchor('manage_teams', 'Manage', 'class="navbar-item"'); ?>
			<?php echo anchor('Stats', 'Statistics', 'class="navbar-item"'); ?>
			<?php if ($this->session->privileges == 'admin') echo anchor('Admin', 'Admin', 'class="navbar-item"'); ?>
		</div>
		<div class="navbar-end">
			<?php echo anchor('Search', 'Search', 'class="navbar-item"'); ?>
			<div class="navbar-item has-dropdown is-hoverable">
				<?php echo anchor('Account', 'Account', 'class="navbar-link"');?>
				<div class="navbar-dropdown">
					<?php echo anchor('Account/settings', 'Settings', 'class="navbar-item"')?>
					<?php echo anchor('Account/admin-settings', 'Admin Settings', 'class="navbar-item"')?>
				</div>
			</div>
			<?php echo anchor('logout', 'Logout', 'class="navbar-item"'); ?>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo base_url('js/menu.js')?>"></script>
</nav>

