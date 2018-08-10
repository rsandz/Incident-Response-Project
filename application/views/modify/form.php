<section class="section">
	<h1 class="title"><?php echo "You are Modifying Item #{$key} in the ".humanize($table).' table'?></h1>
	<hr>
	<?php echo validation_errors('<div class="notification is-danger">', '</div>');?>
	<?php echo form_open("Modify/{$table}/{$key}"); ?>
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
	<hr>

	<div class="level">
		<div class="level-left">
			<div class="level-item">
				<div class="field is-grouped">
					<div class="control">
						<?php echo anchor("Modify/table/{$table}", 'Cancel', 'class="button is-danger is-medium"');?>
					</div>
					<div class="control">
						<?php echo form_submit('modify', 'Modify', 'class="button is-info is-medium"');?>
					</div>
				</div>
			</div>
		</div>
		<div class="level-right">
			<div class="level-item">
				<input type="reset" class="button is-danger">
			</div>
		</div>
	</div>
</section>