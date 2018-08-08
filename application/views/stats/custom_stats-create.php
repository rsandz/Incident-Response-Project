<section class="section content">
	<!-- Form Errors -->
	<div class="columns is-centered">
		<div class="column is-three-quarters">
			<?php echo validation_errors('<div class="notification is-danger">', '</div>') ?>
		</div>
	</div>
	<!-- End of Form Errors-->

	<?php echo form_open('Stats/custom/'.$index); ?>
		<div class="columns is-centered">
			<div class="column is-three-quarters ">
				<div class="columns">
					<div class="column">
						<!--KeyWord Search-->
						<div class="field is-horizontal">
							<div class="field-label">
								<label class="label">Keywords: </label>
							</div>
							<div class="field-body">
								<div class="field">
									<div class="control is-expanded"><input class="input" type="text" name="keywords" value="<?=set_value('keywords')?>"></div>
								</div>
							</div>
						</div>
						<div class="field is-horizontal">
							<div class="field-label"><label class="label">Search in:</label></div>
							<div class="field-body">
								<div class="field">
									<div class="control">
										<label class="checkbox"><input name="kfilters[]" type="checkbox" class="checkbox" value="name" <?=set_checkbox('kfilters[]', 'name', TRUE)?>>Name</label>
										<label class="checkbox"><input name="kfilters[]" type="checkbox" class="checkbox" value="team_name" <?=set_checkbox('kfilters[]', 'team_name', TRUE)?>>Team</label>
										<label class="checkbox"><input name="kfilters[]" type="checkbox" class="checkbox" value="project_name" <?=set_checkbox('kfilters[]', 'project_name', TRUE)?>>Project</label>
										<label class="checkbox"><input name="kfilters[]" type="checkbox" class="checkbox" value="action_name" <?=set_checkbox('kfilters[]', 'action_name', TRUE)?>>Action</label>
										<label class="checkbox"><input name="kfilters[]" type="checkbox" class="checkbox" value="type_name" <?=set_checkbox('kfilters[]', 'type_name', TRUE)?>>Action Type</label>
										<label class="checkbox"><input name="kfilters[]" type="checkbox" class="checkbox" value="log_desc" <?=set_checkbox('kfilters[]', 'log_desc', TRUE)?>>Log Description</label>
									</div>
								</div>
							</div>
						</div>
						<div class="field is-horizontal">
							<div class="field-label"><label class="label">Search Type</label></div>
							<div class="field-body">
								<div class="field">
									<div class="control">
										<label class="radio"><input name="ksearch_type" type="radio" class="radio" value="all" <?=set_checkbox('ksearch_type', 'all', TRUE)?>>All Keywords</label>
										<label class="radio"><input name="ksearch_type" type="radio" class="radio" value="any" <?=set_checkbox('ksearch_type', 'any')?>>Any Keyword</label>
									</div>
								</div>
							</div>
						</div>
						<p class="is-danger"><?=form_error('kfilters[]')?></p>
					</div>
					<div class="column is-narrow">
						<input class="button is-primary" type="submit" name="submit" value="Save">
					</div>
				</div>
			</div>
		</div>


		<div class="columns is-centered">
			<div class="column is-three-quarters">
				<div class="field">
					<div class="control">
						<label class="label is-large"><button type="button" class="button" style="margin-right: 5px" id="filterBtn">
							<span class="fas fa-chevron-down"></span></button>
							Filters
						</label>
					</div>
				</div>
			</div>
		</div>
		<!-- Filter Content -->
		<div class="columns is-centered">
			<div class="column is-three-quarters">
				<div class="box" id="filterBox"> 
					<div class="columns">
						<div class="column">
							<h3>Action Types:</h3>
							<div class="field is-grouped is-grouped-multiline">
								<?php foreach($action_types as $action_type):?>
									<div class="control">
										<label class="label"><?php echo $action_type->type_name?>
										<?php echo form_checkbox('action_types[]', $action_type->type_id);?>
										</label>
									</div>
								<?php endforeach;?>
							</div>
						</div>
						<div class="column">
							<h3>Projects:</h3>
							<div class="field is-grouped is-grouped-multiline">
								<?php foreach($projects as $project):?>
									<div class="control">
										<label class="label"><?php echo $project->project_name?>
										<?php echo form_checkbox('projects[]', $project->project_id);?>
										</label>
									</div>
								<?php endforeach;?>
									<!-- No Project Condition -->
								<div class="control">
									<label class="label checkbox">
										No Project
										<input class="checkbox" type="checkbox" name="null_projects" value=TRUE >
									</label>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="columns">
						<div class="column">
							<h3>Teams:</h3>
							<div class="field">
								<?php if(!empty($teams)): ?>
									<?php foreach($teams as $team):?>
										<div class="control">
											<label class="label"><?php echo $team->team_name?>
											<?php echo form_checkbox('teams[]', $team->team_id);?>
											</label>
										</div>
									<?php endforeach;?>
								<?php endif;?>
								<!-- No Team Condition -->
								<div class="control">
									<label class="label checkbox">
										No Team 
										<input class="checkbox" type="checkbox" name="null_teams" value=TRUE>
									</label>
								</div>
							</div>
						</div>
						<!--User Selection--> 
						<div class="column">
							<label class="label" for="users">Users:</label>
							<div class="select is-multiple is-expanded">
								<select name="users[]" id="users" multiple="multiple">
									<?php foreach($users as $user): ?>
										<option value="<?=$user->user_id?>"><?=$user->name?></option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
					</div>
					<div class="notification is-info">
						<span class="delete"></span>
						<p>Leave checkboxes blank to not use that filter.</p>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>

<script>
	$(function() 
	{	
		//User Selection dropbox
		$('#users').select2({
			placeholder: "Select Users..",
			multiple: true,
			width: '100%'
		});
		console.log($('#users').val());

		//For Filter Box
		$('#filterBox').hide(); // Need to hide filter box AFTER select 2
		$('#filterBtn').click(function() 
		{	
			$('#filterBox').slideToggle('fast');
		});
	});
</script>