<div class="section">
	<div class="container">
		<h1 class="title"><?php echo "You are Modifying Item #{$key} in the ".humanize($table).' table'?></h1>
		<hr>
		<?php echo validation_errors('<div class="notification is-danger">', '</div>');?>
		<?php echo form_open("modify/{$table}/{$key}"); ?>
		<?php foreach($fields as $field):?>
			<div class="field">
				<label class="label">
					<?php echo humanize($field->name) ?>
				</label>
				<div class="control">
					<?php echo $field->form?>
				</div>
			</div>
		<?php endforeach;?>

		<?php echo form_submit('modify', 'Modify', 'class="button is-info"');?>
		<?php echo anchor("modify/table/{$table}", 'Cancel', 'class="button is-danger"');?>

	</div>
</div>