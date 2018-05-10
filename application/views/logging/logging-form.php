<body>
	<div class="hero is-success">
		<div class="hero-body">
			<h1 class="title">Logging Form</h1>
		</div>
	</div>

	<div class="section">
	<div class="container">
		<?php echo form_open('logging/log', 'class="pure-form pure-form-stacked"'); ?>

		<div class="field">
			<label class="label">Actions</label>
			<!-- Creates the selection for actions -->
			<div class="control">
				<div class="select"> 
					<select name="action">
						<?php foreach ($actions as $action_row): ?>
							<?php echo '<option value="'.$action_row['action_id'].'">'.$action_row['action_name'].'</option><br>' ?>
						<?php endforeach ?>
					</select><br>
				</div>
			</div>
		</div>

		<div class="field">
			<label class="label "for="date">Date</label>
			<div class="control">
				<input type="date" name="date">
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