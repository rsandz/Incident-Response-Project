<div class="section" style="padding-top:0">
	<div class="container">
		<div class="content">
			<h2 class="subtitle">Select a Team to Manage</h2>
			<?php foreach ($teams as $index => $team): ?>
				<div class="box">
					<h3>Team Name: <?php echo $team->team_name?></h3>
					<div class="level">
						<div class="level-left">
							
						</div>
						<div class="level-right">
							<?php echo $team_modify_links[$index];?>
						</div>
					</div>
				</div>
			<?php endforeach ?>
		</div>
	</div>
</div>
