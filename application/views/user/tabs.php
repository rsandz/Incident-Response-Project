<div class="tabs">
	<ul>
		<li class="<?php if ($title == 'Dashboard') {echo 'is-active';}?>">
			<?php echo anchor('Dashboard', 'Previous Entries');?>
		</li>
		<li class="<?php if ($title == 'My Info') {echo 'is-active';}?>">
			<?php echo anchor('Dashboard/my_info', 'My Info');?>
		</li>
	</ul>
</div>