<body>
	<div class="hero is-primary">
		<div class="hero-body">
			<h1 class="title">Aministrative Dashboard</h1>
		</div>
	</div>

	<div class="section">
	<div class="column box is-half is-offset-one-quarter">
		<div class="columns is-half is-offset-one-quarter">
		<div class="column has-text-centered">
			<?php echo anchor('admin/view-logs', 'View Logs', 'class="button is-primary"')?>
		</div>



		<div class="column">
			<?php echo form_open('admin/create'); ?>
				<div class="field">
				<label class="label">Create New:</label>
				<div class="control">
					<select class="select" name="type">
						<option value="users">User</option>
						<option value="actions">Action</option>
						<option value="teams">Team</option>
						<option value="projects">Project</option>
					</select>
				</div>
				</div>

				<div class="field">
				<div class="control">
					<input class="button is-info" type="submit" name="submit" value="Create New">
				</div>
				</div>
			</form>
		</div>
		</div>
		</div>
		</div>

	<div class="section">
	<div class="box column is-half is-offset-one-quarter">
	<div class="content">
		<div class="control">
			<?php echo anchor('home', 'Dashboard', 'class="button is-success"');?>
		</div>
	</div>
	</div>
	</div>
</body>