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
						<?php echo anchor('Incidents/view-reports', 'View Reports', 'class="button is-light is-fullwidth"');?>
					</div>
					<div class="panel-block">
						<?php echo anchor('Incidents/run', 'Manually Run/Rerun', 'class="button is-light is-fullwidth"');?>
					</div>
				</nav>
			</div>
		</div>
	</div>
</div>