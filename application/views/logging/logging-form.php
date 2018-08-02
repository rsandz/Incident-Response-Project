<?php echo script_tag('js/descriptions.js')?>
<body>
	<div class="section">
	<div class="container">
		<div class="column">
			<span class=""><?php echo validation_errors('<div class="notification is-danger">', '</div>'); ?></span>
			<span class="has-text-danger" id="errors"></span>
		</div>
		<?php echo form_open('logging/log'); ?>
				
			<div class="columns">
				
			<div class="column">
			<div class="field">
				<label class="label">Project: <span class="has-text-danger">(Required)</span></label>
				<!-- Creates the selection for Projects -->
				<div class="control">
					<div class="select"> 
						<select name="project" id="project-selector">
							<?php foreach ($projects as $project_row): ?>
								<?php echo '<option value="'.$project_row->project_id.'">'.$project_row->project_name.'</option><br>' ?>
							<?php endforeach ?>
						</select>
					</div>
				</div>
			</div>
			</div>
				
			<!-- PROJECT DESCRIPTION -->
			<div class="column">
				<div class="content">
					<p id="project-desc"></p>
				</div>
			</div>
				
			<!-- Extra Column -->
			<div class="column"></div> 
				
			</div>
			
			<hr>

			<div class="field">
				<label class="label">Team:</label>
				<!-- Creates the selection for Teams -->
				<div class="control">
					<div class="select"> 
						<select name="team">
							<?php if(!empty($teams)):?>
								<?php foreach ($teams as $team_row): ?>
									<?php echo '<option value="'.$team_row->team_id.'">'.$team_row->team_name.'</option><br>' ?>
								<?php endforeach ?>
							<?php endif;?>
							<option value='null'>No Team</option>
						</select>
					</div>
				</div>
			</div>

			<hr>
				
			<div class="columns">
		
			<!-- Action Type -->
			<div class="column">
				<label class="label">Action Type: <span class="has-text-danger">(Required)</span></label>
				<div class="select">
					<select name="action_type" class="select" id="type-selector">
						<?php foreach ($types as $type): ?>
							<?php echo '<option value="'.$type->type_id.'">'.$type->type_name.'</option><br>' ?>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		
			<!-- Action Selection -->
			<div class="column">
			<div class="field">
				<label class="label">Actions: <span class="has-text-danger">(Required)</span></label>
				<!-- Creates the selection for actions -->
				<div class="control">
					<div class="select" id="action-div"> 
					</div>
				</div>
			</div>
			</div>
				
				
			<!-- ACTION DESCRIPTION -->
			<div class="column">
				<div class="content">
					<p id="action-desc"></p>
				</div>
			</div>
			</div>

			<hr>
			
			<div class="columns">
				<div class="column">
					<div class="field">
						<label class="label "for="date">Date: <span class="has-text-danger">(Required)</span></label>
						<div class="control"> 
							<input class="input" type="date" name="date" value="<?php echo date('Y-m-d');?>">
						</div>
					</div>
				</div>
				<div class="column">
					<div class="field">
						<label class="label "for="date">Time: <span class="has-text-danger">(Required)</span></label>
						<div class="control">
							<input class="input" type="time" name="time" value = "<?php echo date('H:i');?>">
						</div>
					</div>
				</div>
				<div class="column is-2">
					<div class="field">
						<label class="label">Number of Hours: </label>
						<div class="control">
							<input type="number" class="input" value="0" name="hours">
						</div>
					</div>
				</div>
			</div>
			
			<div class="field">
				<label class="label" ">Description:</label>
				<div class="control ">
					<textarea class="textarea" name="desc"></textarea>
				</div>
				<p class="has-text-right">Supports some HTML Markups. Click <?php echo anchor('Help/markups', 'here');?> to learn more.</p>
			</div>
				
			<hr>

			<div class="level">
				<div class="level-left">
						<div class="field is-grouped">
							<div class="control">
								<p class="button is-danger is-medium">Reset</p>
							</div>
							<div class="control">
								<input type="submit" class = "button is-info is-medium" name="submit" value="New Log" />
							</div>
						</div>
				</div>
				<div class="level-right">
					<div class="level-item">
					</div>
				</div>
			</div>
		</div>

		</form>
				</div>
				</div>
	</div>
	</div>



</body>
