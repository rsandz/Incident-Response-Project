<div class="section">
	<div class="container">
		<?php echo isset($errors) ? $errors : NULL?>
		<h2 class="title">Create a New Incident</h2>
		<hr>
		<!-- Form --->
		<?php echo form_open('Incidents/create');?>
			<div class="field">
				<div class="control">
					<label class="label">Name: </label>
					<input type="text" class="input" name="incident_name" required>
				</div>
			</div>
			<div class="field is-grouped">
				<div class="control is-expanded">
					<label class="label">Date: </label>
					<input type="date" class="input is-fullwidth" name="incident_date" id='date' required>
				</div>
				<div class="control is-expanded">
					<label class="label">Time: </label>
					<input type="time" class="input is-fullwidth" id='time' name="incident_time" >
				</div>
				<div class="control" style="margin-top: auto">
					<a class="button is-info" id='now'>Now</a>
				</div>
			</div>
			<div class="field">
				<div class="control">
					<label class="label">Description:</label>
					<textarea class="textarea" placeholder="Description" name="incident_desc"></textarea>
					<p class="has-text-right">Supports HTML Markups. Click here for more Information</p> <!-- TODO: add the link-->
				</div>
			</div>
			<div class="level">
				<div class="level-left">
					<?php echo anchor('Incidents', 'Cancel', 'class="button is-danger"')?>
				</div>
				<div class="level-right">
					<?php echo form_submit('Submit', 'Submit', 'class="button is-info"');?>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$(function()
	{
		$('#now').click(function()
			{
				let now = new Date();
				let nowDate = now.getFullYear() 
					+ '-' + (now.getMonth() + 1).toString().padStart(2, '0') 
					+ '-' + now.getDate().toString().padStart(2, '0');
				let nowTime = now.getHours().toString().padStart(2, '0') 
					+ ':' + now.getMinutes().toString().padStart(2, '0') 
				$('#date').val(nowDate);
				$('#time').val(nowTime);
			});
	});
</script>