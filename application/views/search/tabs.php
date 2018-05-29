<div class="tabs">
	<ul>
		<li class="<?php if ($title == 'Search') {echo 'is-active';}?>">
			<?php echo anchor('Search', 'Search');?>
		</li>
		<li class="<?php if ($title == 'View Tables') {echo 'is-active';}?>">
			<?php echo anchor('Search/view_tables', 'View Tables');?>
		</li>
	</ul>
</div>