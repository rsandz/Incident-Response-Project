<body>
<div class="content">
	<div class="hero is-primary"> 
	<div class="hero-body">
		<h1 class="title"><?=$title?> Form</h1>
	</div>
	</div>

	<div class="section">	

	<?php echo form_open('Admin/create') ?>

	<?php foreach ($field_data as $field): ?>

		<?php if ($field->type == 'enum'): ?>
			<label class="label"> <?php echo humanize($field->name) ?> </label>

			<div class="field">
			<div class="control">
				<?php foreach ($field->enum_vals as $value): ?>
					<label><?php echo $value?></label>
					<?php echo form_radio($field->name, $value);?>
				<?php endforeach; ?>
			</div>
			</div>

		<?php elseif ($field->type == 'binary'):?>
			<label class="label"> <?php echo humanize($field->name) ?> </label>

			<div class="field">
			<div class="control">
				<label>True</label>
				<input type="radio" name=<?php echo '"'.$field->name.'"'?> value="1">
				<label>False</label>
				<input type="radio" name=<?php echo '"'.$field->name.'"'?> value="0">
			</div>
			</div>

		<?php else: ?>
			<label class="label"> <?php echo humanize($field->name) ?> </label>

			<div class="field">
			<div class="control">
			<?php echo form_input($field->name, '', 'class="input"'); ?>
			</div>
			</div>

		<?php endif; ?>

	<?php endforeach; ?>

	<?php echo validation_errors();?>

	<div class="field">
	<div class="control">
		<input class="button is-primary" type="submit" name="submit" value=<?php echo '"Create '.singular($type).'"' ?>>
	</div>
	</div>


	</form>
	</div>

</div>
</body>