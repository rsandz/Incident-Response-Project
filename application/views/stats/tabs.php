<div class="tabs">
	<ul>
		<li class="<?php if ($title == 'My Statistics') {echo 'is-active';}?>">
			<?php echo anchor('stats/my_stats', 'My Statistics');?>
		</li>
		<li class="<?php if ($title == 'Project Statistics') {echo 'is-active';}?>">
			<?php echo anchor('stats/project_stats', 'Project Statistics');?>
		</li>
		<li class="<?php if ($title == 'Team Statistics') {echo 'is-active';}?>">
			<?php echo anchor('stats/team_stats', 'Team Statistics');?>
		</li>
		<li class="<?php if ($title == 'Custom Statistics 1') {echo 'is-active';}?>">
			<?php echo anchor('stats/custom/1', 'Custom Statistics 1');?>
		</li>
		<li class="<?php if ($title == 'Custom Statistics 2') {echo 'is-active';}?>">
			<?php echo anchor('stats/custom/2', 'Custom Statistics 2');?>
		</li>
		<li class="<?php if ($title == 'Compare') {echo 'is-active';}?>">
			<?php echo anchor('stats/compare', 'Compare');?>
		</li>
	</ul>
</div>