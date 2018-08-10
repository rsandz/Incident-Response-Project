<nav class="navbar is-dark is-medium" role="navigation" aria-label="main navigation">
	<div class="navbar-brand">
		<a class="navbar-item" href="<?php echo site_url('Dashboard')?>">
			<img src="<?php echo assets_url('img/logo.png')?>" alt="Incident Response Project">
		</a>

		<!-- For Mobile -->
		<?php echo anchor('Search', 'Search', 'class="navbar-item is-hidden-tablet"'); ?>
		<!-- END Mobile -->

		<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
		  <span aria-hidden="true" class="has-background-white"></span>
		  <span aria-hidden="true" class="has-background-white"></span>
		  <span aria-hidden="true" class="has-background-white"></span>
		</a>
	</div>
	<div class="navbar-menu">
		
		<!-- For Mobile -->
		<div class="navbar-start is-hidden-tablet">
			<?php echo anchor('Dashboard', 'Dashboard', 'class="navbar-item"'); ?>
			<?php echo anchor('Logging', 'Log an Activity', 'class="navbar-item"'); ?>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link">Create</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Create/action', 'Action', 'class="navbar-item"')?>
					<!-- Not Available to Normal Users-->
					<?php if($this->authentication->check_admin()):?>
						<?php echo anchor('Create/action_type', 'Action Type', 'class="navbar-item"');?>
						<?php echo anchor('Create/project', 'Project', 'class="navbar-item"');?>
						<?php echo anchor('Create/team', 'Team', 'class="navbar-item"');?>
						<?php echo anchor('Create/user', 'User', 'class="navbar-item"');?>
					<?php endif;?>
				</div>				
			</div>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link" >
					Manage
				</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Manage/teams', 'Teams', 'class="navbar-item"')?>
				</div>				
			</div>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link" >Statistics</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Stats/my-stats', 'My Stats', 'class="navbar-item"')?>
					<?php echo anchor('Stats/project-stats', 'Project', 'class="navbar-item"')?>
					<?php echo anchor('Stats/team-stats', 'Team', 'class="navbar-item"')?>
					<?php echo anchor('Stats/custom/1', 'Custom 1', 'class="navbar-item"')?>
					<?php echo anchor('Stats/custom/2', 'Custom 2', 'class="navbar-item"')?>
					<?php echo anchor('Stats/compare', 'Compare', 'class="navbar-item"')?>
				</div>				
			</div>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link" >Admin</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Modify', 'Modify Tables', 'class="navbar-item"')?>
					<?php echo anchor('Incidents', 'Incidents', 'class="navbar-item"')?>
				</div>				
			</div>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link">Account</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Account', 'Account Info', 'class="navbar-item"');?>
					<?php echo anchor('Account/settings', 'Settings', 'class="navbar-item"')?>
					<?php echo anchor('Account/admin-settings', 'Admin Settings', 'class="navbar-item"')?>
				</div>
			</div>
		</div>
		<!-- END For Mobile -->

		<!-- For Desktop -->
		<div class="navbar-end is-hidden-mobile">
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
		<!-- End for Desktop -->
	</div>
	<?php echo script_tag('js/menu.js')?>
</nav>

