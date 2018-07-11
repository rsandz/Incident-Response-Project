<div class="section" style="padding-top:0">
	<div class="container">
		<div class="content">
			<div class="level">
				<div class="level-left">
					<div class="level-item">
						<h2 class="subtitle">Select a Team to Manage</h2>
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
				<?php foreach($teams as $index => $team):?>
					<div class="column is-4 selection-item">
						<article class="box">
							<h2 class="title is-4">Team Name: <span class="selection-title"><?php echo $team->team_name?></span></h2>
							<hr>
							<div class="content selection-info">
								<ul>
									<li><b>Team Leader: </b><?php echo $team->team_leader_name?></li>
									<li>
										<b>Team Description: </b>
										<p><?php echo $team->team_desc?></p>
									</li>
								</ul>
							</div>
							<div class="level">
								<div class="level-left">
								</div>
								<div class="level-right">
									<div class="level-item">
										<?php echo $team_modify_links[$index];?>
									</div>
								</div>
							</div>
						</article>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url('js/selection-search.js')?>"></script>