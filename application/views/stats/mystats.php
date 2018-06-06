<script type="text/javascript" src="<?php echo base_url('js/Chart.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/chart-controller.js')?>"></script>

<div class="section">
	<div class="container">
		<h1 class="title">My Statistics</h1>
		<hr>
		<h3 class="title is-4">Logging Statistics</h3>
		<div class="columns">
			<div class="column" style="margin-top: 10%">
				<button class="button" id="chart-left"><</button>
			</div>
			<div class="chart-container column is-three-quarters">
				<canvas width="auto" height="auto" id="chart"></canvas>
			</div>
			<div class="column" style="margin-top: 10%">
				<button class="button" id="chart-right">></button>
			</div>
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
							<p class="control"><?php echo form_dropdown('interval_type', $interval_options, NULL,'class="select" id="interval_type"');?></p>
							<span class="icon"><img class="img is-hidden" id="interval_type_loading" src="<?php echo base_url('img/loading.gif')?>"></span>
						</div>
					</div>
				</div>
				<div class="panel-block">
					<div>
						<label class="label">From: </label>
						<input type="date" name="from_date" class="input" value="<?=set_value('from_date')?>">
						<label class="label">To:</label>
						<input type="date" name="to_date" class="input" value="<?=set_value('to_date')?>">
					</div>
				</div>
				<div class="panel-block">
					<div class="control">
						<label for="" class="label">Jump to:</label>
						<input type="date" name="from_date" class="input" value="<?=set_value('from_date')?>">
					</div>
				</div>
				</nav>
			</div>
		</div>
	</div>
</div>