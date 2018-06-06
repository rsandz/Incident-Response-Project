<div class="tabs">
	<ul>
		<li class="<?php if ($title == 'Dashboard') {echo 'is-active';}?>">
			<?php echo anchor('Welcome', 'Previous Entries');?>
		</li>
		<li class="<?php if ($title == 'View Tables') {echo 'is-active';}?>">
			<?php echo anchor('User/mystats', 'My Statistics');?>
		</li>
	</ul>
</div>