<?php echo form_open('manage_teams/remove_users/'.$team_id);?>
<div class="section" style="padding-top:0">
	<div class="container">
		<div class="content">
			<h2>You are Viewing: <?php echo $team_name?></h2>
			<hr>
			<div class="columns">
				<div class="column">
					<h3>Team Members:</h3>
					<?php if (isset($team_members)):?> <!-- Dont Display anything if there are no team members-->
						<?php foreach ($team_members as $name => $id): ?>
							<div class="tag is-medium">
								<label class="checkbox">
									<?php echo form_checkbox('users[]', $id, FALSE, 'class="checkbox"');?> <?php echo $name?>
								</label>
							</div>
						<?php endforeach ?>
					<?php endif;?>
					<hr>
					<div class="level">
						<div class="level-left">
							<div class="level-item">
								<div class="field is-grouped">
									<div class="control">
										<?php echo anchor('manage_teams/add_users/'.$team_id, 'Add User', 'class="button is-info"');?>
									</div>
									<div class="control">
										<button type="button" class="button is-danger" id="delete-users">Remove Users</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="column is-one-third">
					<div class="card">
						<div class="card-header">
							<h3 class="card-header-title">Team Statistics:</h3>
						</div>
						<div class="card-content">
							<div class="content">
								<ul>
									<li>
										<strong>Team Name:</strong>  <?php echo $team_name?>
									</li>
									<li>
										<strong>Team Leader:</strong> <?php echo $team_leader_name?>
									</li>
									<li>
										<strong>Number of Members:</strong> <?php echo $num_members?>
									</li>
									<li>
										<strong>Number of Logs:</strong> <?php echo $team_logs?>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<?php echo anchor('manage_teams', 'Back', 'class="button is-info"');?>
		</div>
	</div>
</div>


<div class="modal" id="delete-confirm">
	<div class="modal-background"></div>
	<div class="modal-card">
		<header class="modal-card-head has-background-info">
			<div class="modal-card-title has-text-white">Confirm Delete</div>
			<button type="button" class="delete modal-cancel" aria-label="close"></button>
		</header>
		<div class="modal-card-foot">
			<div class="is-pulled-right">
				<input class="button is-info" type="submit" name="submit" value="Confirm">
				<button type="button" class="button is-danger modal-cancel">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
</form>

<?php echo script_tag('js/modal-confirm.js')?>