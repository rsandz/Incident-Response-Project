<div class="content section">
	<div class="container">
		<h1>Action Type Creation Form</h1>
		<hr>
		<?php echo form_open('Create/index/action_type', 'class="form"'); ?>
			<div class="field">
				<div class="control">
					<label for="" class="label">Action Type Name</label>
					<input class="input" type="text" name="action_type_name" value="<?php echo set_value('action_type_name')?>">
				</div>
			</div>
			<div class="field">
				<div class="control">
					<label for="" class="label">Action Type Description</label>
					<textarea class="textarea"type="text"  name="action_type_desc" value="<?php echo set_value('action_type_desc')?>"></textarea>
				</div>
			</div>
			<div class="field">
				<div class="control">
					<label class="checkbox">
						<input class="checkbox" name="is_active" type="checkbox" value="1" 
						<?php echo set_checkbox('is_active', 1, TRUE); ?> >
						Is Active?
					</label>
				</div>
			</div>
			<?=form_submit('submit', 'Create', 'class="button is-primary is-medium"');?>
		</form>
	</div>
</div>