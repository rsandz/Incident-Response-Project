<div class="section">
	<div class="container">
		<div class="columns">
				<!-- The Recent Incidents Table -->
			<div class="column recent-incidents-wrapper">
					<h1 class="title">Recent Incidents</h1>
					<?php echo $incidents_table ?: NULL?>
			</div>
			
				<!-- Control Panel -->
			<div class="column is-one-third">
				<nav class="panel ">
					<div class="panel-heading">
						<h2 class="title is-4">Incidents Control Panel</h2>		
					</div>
					<div class="panel-block ">
						<?php echo anchor('Incidents/create', 'New Incident', 'class="button is-light is-fullwidth"');?>
					</div>
					<div class="panel-block">
						<?php echo anchor('Incidents/report/select', 'View Reports', 'class="button is-light is-fullwidth"');?>
					</div>
					<div class="panel-block">
						<?php echo anchor('Account/admin-settings', 'Your Incident Settings', 'class="button is-light is-fullwidth"');?>
					</div>
					<div class="panel-block">
						<?php echo anchor('Incidents/analytics-settings', 'Google Analytics Settings', 'class="button is-light is-fullwidth"');?>
					</div>
					<div class="panel-block">
						<?php echo anchor('Cron/incident_check', 'Manually Run Analytics', 'class="button is-light is-fullwidth"');?>
					</div>
				</nav>
				<nav class="panel">
					<div class="panel-heading">
						<h2 class="title is-4">Incidents Statistics</h2>
					</div>
					<div class="panel-block">
						<p class="has-text-weight-bold">Last Incident:&nbsp</p>
						<?php echo $stats['last_incident']?>
					</div>
					<div class="panel-block">
						<p class="has-text-weight-bold">Total Incidents:&nbsp</p>
						<?php echo $stats['total_incidents']?>
					</div>
					<div class="panel-block"></div>
				</nav>
			</div>
		</div>
	</div>
</div>