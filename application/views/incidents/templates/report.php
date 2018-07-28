<div class="content" id="report">
	<?php echo $title?>
	<hr>
	<?php echo $summary?>
	<hr>
	<div class="columns">
		<div class="column">
			<h2 class="subtitle">Recent Logs (Past 10 Logs)</h2>
			<?php echo $last_10_table?>
		</div>
		<div class="column">
			<h2 class="subtitle">Hour in the past week</h2>
		</div>
	</div>
	<h2 class="subtitle">Quick Searches</h2>
	<hr>
	<div class="columns">
		<div class="column">
			<?php echo $past_week_search?>
		</div>
		<div class="column">
			<?php echo $past_month_search?></div>
		<div class="column"></div>
	</div>
</div>