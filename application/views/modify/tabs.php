<div class="tabs">
	<ul>
		<li class="<?php  if (substr($title, 0, 6) == 'Modify') {echo 'is-active';}?>">
			<?php echo anchor('modify', 'Modify Tables');?>
		</li>
		<li class="<?php if (substr($title, 0, 6) == 'Manage') {echo 'is-active';}?>">
			<?php echo anchor('manage_teams', 'Manage Teams');?>
		</li>
	</ul>
</div>