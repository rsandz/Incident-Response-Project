<div class="tabs">
	<ul>
		<li class="<?php if ($title == 'Dashboard') {echo 'is-active';}?>">
			<?php echo anchor('Home', 'Previous Entries');?>
		</li>
		<li class="<?php if ($title == 'My Info') {echo 'is-active';}?>">
			<?php echo anchor('User/my_info', 'My Info');?>
		</li>
	</ul>
</div>