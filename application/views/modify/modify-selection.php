<section class="section">
	<div class="container">
		<div class="content">
			<h2 class="subtitle">Choose a table to modify</h2>
			<hr>
			<div class="columns is-multiline is-desktop">
				<?php foreach($tables as $table):?>
					<div class="column is-4">
						<div class="box selection-item">
							<h2 class="title is-4 selection-title"><?php echo humanize($table)?></h2>
							<hr>
							<div class="level">
								<div class="level-left">
									<p></p>
								</div>
								<div class="level-right">
									<div class="level-item">
										<?php echo anchor("Modify/table/{$table}", 'Modify', 'class="button is-info"');?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach;?>
			</div>
		</div>
		<div class="control">
			<?php echo anchor('admin', 'Return to Admin', 'class="button is-info"');?>
		</div>
	</div>
</section>