<body>
	<!-- Tabs -->
	
	<div class="tabs">
		<ul>
			<li <?php if ($type == 'action') {echo 'class="is-active"';}?>>
				<?php echo anchor('Create/index/action', 'Action');?>
			</li>
			<!-- Not Available to normal Users -->
			<?php if ($this->session->privileges !== 'user'): ?>
				<li <?php if ($type == 'project') {echo 'class="is-active"';}?>>
					<?php echo anchor('Create/index/project', 'Project');?>
				</li>
				<li <?php if ($type == 'team') {echo 'class="is-active"';}?>>
					<?php echo anchor('Create/index/team', 'Team');?>
				</li>
				<li <?php if ($type == 'user') {echo 'class="is-active"';}?>>
					<?php echo anchor('Create/index/user', 'User');?>
				</li>
				
			<?php endif;?>
		</ul>
	</div>