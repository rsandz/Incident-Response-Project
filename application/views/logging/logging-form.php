<body>

	<?php echo form_open('logging/log', 'class="pure-form pure-form-stacked"'); ?>

	<legend>Logging Form</legend><br>
	<?php echo validation_errors(); ?>

	<label>Actions</label><br>

	<!-- Creates the selection for actions -->
	<select name="action">
		<?php foreach ($actions as $action_row): ?>
			<?php echo '<option value="'.$action_row['action_id'].'">'.$action_row['action_name'].'</option><br>' ?>
		<?php endforeach ?>
	</select><br>

	
	<div>
	    <label for="date">Date</label>
	    <input type="date" name="date">

	    <label for="desc">Description</label>
	    <textarea name="desc"></textarea>

	    <label for="user_id">PLACEHOLDER: User ID</label>
	    <input type="text" name="user_id">

	    <input type="submit" class = "pure-button pure-button-primary" name="submit" value="New Log" />
    </div>

	</form>



</body>
</html>