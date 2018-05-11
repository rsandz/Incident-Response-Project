<body>
	<div class="hero is-success">
		<div class="hero-body">
			<h1 class="title">Logging Form</h1>
		</div>
	</div>

	<div class="section">
	<div class="container">
		<?php echo form_open('logging/log'); ?>

		<div class="field">
			<label class="label">Project</label>
			<!-- Creates the selection for Projects -->
			<div class="control">
				<div class="select"> 
					<select name="project">
						<?php foreach ($projects as $project_row): ?>
							<?php echo '<option value="'.$project_row['project_id'].'">'.$project_row['project_name'].'</option><br>' ?>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

		<div class="field">
			<label class="label">Team</label>
			<!-- Creates the selection for Teams -->
			<div class="control">
				<div class="select"> 
					<select name="team">
						<?php foreach ($teams as $team_row): ?>
							<?php echo '<option value="'.$team_row['team_id'].'">'.$team_row['team_name'].'</option><br>' ?>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

		<div class="field">
			<label class="label">Actions</label>
			<!-- Creates the selection for actions -->
			<div class="control">
				<div class="select"> 
					<select name="action">
						<?php foreach ($actions as $action_row): ?>
							<?php echo '<option value="'.$action_row['action_id'].'">'.$action_row['action_name'].'</option><br>' ?>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

		<?php date_default_timezone_set($time_zone); ?> <!-- SETS DEFAULT TIME ZONE-->
		<div class="field">
			<label class="label "for="date">Date</label>
			<div class="control">
				<input type="date" name="date" value="<?php echo date('Y-m-d');?>">
			</div>
		</div>
		<div class="field">
			<label class="label "for="date">Time</label>
			<div class="control">
				<input type="time" name="time" value = "<?php echo date('H:i');?>">
			</div>
		</div>
		<div class="field">
			<label for="desc">Description</label>
			<div class="control">
				<textarea class="textarea" name="desc"></textarea>
			</div>
		</div>

		<div class="field">
		<input type="submit" class = "button is-primary" name="submit" value="New Log" />
		</div>


		<span class="has-text-danger"><?php echo validation_errors(); ?></span>

	</form>
	</div>
	</div>



</body>
</html>