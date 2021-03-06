<section class="section">
	<div class="container">
		<h1 class="title">Compare Statistics</h1>
		<hr>
	</div>

	<?php echo script_tag('js/Chart.js')?>
	<?php echo script_tag('js/chart-controllers/chart-controller-compare.js')?>
	<?php echo script_tag('js/myChart.js')?>
	<?php echo script_tag('js/moment.js')?>

	<div class="container">
		<h3 class="title is-4">Logging Statistics</h3>
		<div class="columns">
			<div class="column is-narrow">
				<button class="button" id="chart-left1"><</button>
			</div>
			<div class="chart-container column" style="min-width: 30%">
				<canvas id="logs-chart"></canvas>
			</div>
			<div class="column is-narrow">
				<button class="button" id="chart-right1">></button>
			</div>
			<div class="column is-narrow">
				<nav class="panel">
					<p class="panel-heading">
						Chart Controls
					</p>
					<div class="panel-block">
						<div class="level">
							<div class="level-left">
								<label class="label" style="margin-right: 5px">Date Interval: </label>
							</div>
							<div class="level-right">
								<p class="control"><?php echo form_dropdown('interval_type1', $interval_options, NULL,'class="select" id="interval_type1"');?></p>
							</div>
						</div>
					</div>
					<div class="panel-block">
						<div class="control">
							<label for="" class="label">Jump to:</label>
							<div class="field has-addons">
								<div class="control">
									<input type="date" id="jump-date1" name="jump-date" class="input" value="">
								</div>
								<div class="control">
									<p class="button" id="jump1">Jump</p>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-block">
						<div class="control">
							<label for="" class="label">Number of Datapoints:</label>
							<div class="field has-addons">
								<div class="control">
									<input type="number" id="limit-num1" name="limit-num" class="input" value="10">
								</div>
								<div class="control">
									<p class="button" id="limit1">Set</p>
								</div>
							</div>
						</div>
					</div>
				</nav>
			</div>
		</div>
		<h3 class="title is-4">Hours Statistics</h3>
		<div class="columns">
			<div class="column is-narrow">
				<button class="button" id="chart-left2"><</button>
			</div>
			<div class="chart-container column" style="min-width: 30%">
				<canvas id="hours-chart"></canvas>
			</div>
			<div class="column is-narrow">
				<button class="button" id="chart-right2">></button>
			</div>
			<div class="column is-narrow">
				<nav class="panel">
					<p class="panel-heading">
						Chart Controls
					</p>
					<div class="panel-block">
						<div class="level">
							<div class="level-left">
								<label class="label" style="margin-right: 5px">Date Interval: </label>
							</div>
							<div class="level-right">
								<p class="control"><?php echo form_dropdown('interval_type2', $interval_options, NULL,'class="select" id="interval_type2"');?></p>
							</div>
						</div>
					</div>
					<div class="panel-block">
						<div class="control">
							<label for="" class="label">Jump to:</label>
							<div class="field has-addons">
								<div class="control">
									<input type="date" id="jump-date2" name="jump-date2" class="input" value="">
								</div>
								<div class="control">
									<p class="button" id="jump2">Jump</p>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-block">
						<div class="control">
							<label for="" class="label">Number of Datapoints:</label>
							<div class="field has-addons">
								<div class="control">
									<input type="number" id="limit-num2" name="limit-num" class="input" value="10">
								</div>
								<div class="control">
									<p class="button" id="limit2">Set</p>
								</div>
							</div>
						</div>
					</div>
				</nav>
			</div>
		</div>
	</div>
</section>