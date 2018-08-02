<div class="section">
	<div class="container">
		<div class="level">
			<div class="level-left">
				<div class="level-item">
					<h1 class="title">Select a project</h1>
				</div>
			</div>
			<div class="level-right">
				<div class="level-item">
					<div class="control has-icons-left">
						<input id="selection-search" class="input" type="text" name="Search" placeholder="Search">
						<span class="icon is-small is-left">
						      <i class="fas fa-search"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="columns is-multiline">
			<?php foreach($projects as $project):?>
				<div class="column is-4 selection-item">
					<article class="box">
						<h2 class="title selection-title is-4"><?php echo humanize($project->project_name)?></h2>
						<hr>
						<div class="content selection-info">
							<ul>
								<li><b>Project Leader:</b> <?php echo $project->project_leader_name?></li>
								<li><b>Project Description:</b> <?php echo $project->project_desc?></li>
							</ul>
						</div>
						<div class="level">
							<div class="level-left">
							</div>
							<div class="level-right">
								<div class="level-item">
									<?php echo anchor("stats/project_stats/{$project->project_id}", 'View', 'class="button is-info"');?>
								</div>
							</div>
						</div>
					</article>
				</div>
			<?php endforeach;?>
		</div>
	</div>
</div>

<?php echo script_tag('js/selection-search.js')?>