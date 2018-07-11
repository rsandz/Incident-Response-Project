<div class="tabs">
	<ul>
		<li class="<?php  if (substr($title, 0, 6) == 'Modify') {echo 'is-active';}?>">
			<?php echo anchor('modify', 'Modify Tables');?>
		</li>
		<li class="<?php  if (substr($title, 0, 6) == 'Incidents') {echo 'is-active';}?>">
			<?php echo anchor('incidents', 'Incidents');?>
		</li>
	</ul>
</div>