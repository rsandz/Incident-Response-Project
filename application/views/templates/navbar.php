<nav class="navbar is-dark is-medium" role="navigation" aria-label="main navigation">
	<div class="navbar-brand">
		<a class="navbar-item" href="<?php echo site_url('Dashboard')?>">
			<img src="<?php echo assets_url('img/logo.png')?>" alt="Incident Response Project">
		</a>

		<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
		  <span aria-hidden="true" class="has-background-white"></span>
		  <span aria-hidden="true" class="has-background-white"></span>
		  <span aria-hidden="true" class="has-background-white"></span>
		</a>
	</div>
	<div class="navbar-menu">
		
		<!-- For Mobile -->
		<div class="navbar-start is-hidden-tablet">
			<div class="navbar-item">
				<?php echo form_open('Search/result'); ?>
					<div class="field has-addons">
						<div class="control is-expanded">
							<input class="input" type="text" placeholder="Search for Logs" 
							name="keywords" autocomplete='off'>
						</div>
						<div class="control">
							<button class="button">
								<span class="icon is-small"><i class="fas fa-search"></i></span>
							</button>
						</div>
					</div>
					<input type="text" name="kfilters" value="all" hidden>
				</form>
			</div>
			<a href="<?php echo site_url('Dashboard')?>" class="navbar-item">
				<span class="icon"><i class="fa fa-home"></i></span>
				Dashboard
			</a>
			<a href="<?php echo site_url('Logging')?>" class="navbar-item">
				<span class="icon"><i class="fas fa-pencil-alt"></i></span>
				Log an Activity
			</a>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link">
					<span class="icon"><i class="fas fa-cogs"></i></span>
					Create
				</a>
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
					<span class="icon"><i class="fas fa-users"></i></span>
					Manage
				</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Manage/teams', 'Teams', 'class="navbar-item"')?>
				</div>				
			</div>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link" >
					<span class="icon"><i class="fas fa-chart-bar"></i></span>	
					Statistics
				</a>
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
				<a class="navbar-link" >
					<span class="icon"><i class="fas fa-key"></i></span> 
					Admin
				</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Modify', 'Modify Tables', 'class="navbar-item"')?>
					<?php echo anchor('Incidents', 'Incidents', 'class="navbar-item"')?>
					<?php echo anchor('Admin/site-settings', 'Site Settings', 'class="navbar-item"')?>
				</div>				
			</div>
			<div class="navbar-item has-dropdown is-hoverable has-sub-menu">
				<a class="navbar-link">
					<i class="fas fa-user"></i>
					Account
				</a>
				<div class="navbar-dropdown sub-menu">
					<?php echo anchor('Account', 'Account Info', 'class="navbar-item"');?>
					<!-- <?php echo anchor('Account/settings', 'Settings', 'class="navbar-item"')?> -->
					<?php echo anchor('Account/admin-settings', 'Admin Settings', 'class="navbar-item"')?>
				</div>
			</div>
			<a href="<?php echo site_url('Search')?>" class="navbar-item">
				<span class="icon"><i class="fas fa-search"></i></span>
				Advanced Search
			</a>
			<a href="<?php echo site_url('logout')?>" class="navbar-item">
				<span class="icon"><i class="fas fa-sign-out-alt"></i></span>
				Logout
			</a>
		</div>
		<!-- END For Mobile -->

		<!-- For Desktop -->
		<div class="navbar-end is-hidden-mobile">
			<div class="navbar-item has-dropdown is-hoverable">
				<p class="navbar-link">Search</p>
				<div class="navbar-dropdown is-medium">
					<!-- Quick Search -->
					<div class="navbar-item">
						<?php echo form_open('Search/result'); ?>
							<div class="field has-addons is-marginless">
								<div class="control is-expanded">
									<input class="input force-min-width" type="text" placeholder="Search for Logs" 
									name="keywords" autocomplete='off'>
								</div>
								<div class="control">
									<button class="button">
										<span class="icon is-small"><i class="fas fa-search"></i></span>
									</button>
								</div>
							</div>
							<span class="dropdown-divider"></span>
							<input type="text" name="kfilters" value="all" hidden>
						</form>
					</div>
					<!-- ENd of Quick Search-->
					<?php echo anchor('Search', 'Advanced Search', 'class="navbar-item"'); ?>
				</div>
			</div>
			
			<div class="navbar-item has-dropdown is-hoverable">
				<p class="navbar-link">Account</p>
				<div class="navbar-dropdown">
					<?php echo anchor('Account', 'My Account Info', 'class="navbar-item"')?>
					<!-- <?php echo anchor('Account/settings', 'Settings', 'class="navbar-item"')?> -->
					<?php echo anchor('Account/admin-settings', 'Admin Settings', 'class="navbar-item"')?>
				</div>
			</div>
			<?php echo anchor('logout', 'Logout', 'class="navbar-item"'); ?>
		</div>
		<!-- End for Desktop -->
	</div>
	<?php echo script_tag('js/menu.js')?>
</nav>

