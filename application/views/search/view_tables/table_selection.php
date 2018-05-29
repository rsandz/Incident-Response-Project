<?php
	$this->load->helper('inflector');
?>

<div class="section">
	<div class="container">
		<?php foreach ($tables as $table):?>
			<div class="box content">
				<div class="level">
					<div class="level-left">
						<h3 class="sub-title"><?=humanize($table)?> Table</h3>
					</div>
					<div class="level-right">
						<?=anchor('Search/view_tables/'.$table, 'View Table', 'class="button is-light"');?>
					</div>
				</div>
				<p>
					<strong>Statistics:</strong> <br>
					Number of Rows/Entries: <?php echo $stats[$table]['num_rows'] ?>
				</p>
			</div>
		<?php endforeach;?>
	</div>
</div>

<div class="notification is-info">
	<p>To view the log, use the search tab without any filter parameters.</p>
</div>