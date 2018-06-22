<div class="section" style="padding-top: 0">
	<div class="container">
		<div class="content">
			<h2 class="subtitle">Choose a table to modify</h2>
			<?php foreach($tables as $table):?>
				<div class="box">
					<h2 class="title is-4"><?php echo humanize($table)?></h2>
					<div class="level">
						<div class="level-left">
							<p>Test Data</p>
						</div>
						<div class="level-right">
							<div class="level-item">
								<?php echo anchor("modify/table/{$table}", 'Modify', 'class="button is-info"');?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach;?>
		</div>
	</div>
</div>