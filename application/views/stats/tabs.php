<div class="tabs">
	<ul>
		<li class="<?php if ($title == 'My Statistics') {echo 'is-active';}?>">
			<?php echo anchor('Stats/my_stats', 'My Statistics');?>
		</li>
		<li class="<?php if ($title == 'Project Statistics') {echo 'is-active';}?>">
			<?php echo anchor('Stats/project_stats', 'Project Statistics');?>
		</li>
	</ul>
</div>