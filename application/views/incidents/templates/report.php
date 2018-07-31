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
			<canvas class="static-chart" data-chart='<?php echo json_encode($past_week_hours)?>'></canvas>
		</div>
	</div>
	<h2 class="subtitle">Quick Searches</h2>
	<hr>
	<div class="columns">
		<div class="column">
			<?php echo $past_week_search?>
		</div>
		<div class="column">
			<?php echo $past_month_search?>
		</div>
		<div class="column">
			<?php echo $past_3days_search?>
		</div>
	</div>
</div>

<!-- Static chart JS-->
<?php echo script_tag('js/myChart.js')?>
<!-- Chart.js -->
<?php echo script_tag('js/Chart.js')?>