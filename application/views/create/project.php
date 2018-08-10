<section class="content section">
	<h1>Project Creation Form</h1>
	<hr>
	<?php echo form_open('Create/index/project', 'class="form"'); ?>
		<div class="field">
			<label class="label">Project Name: <span class="has-text-danger">(Required)</span></label>
			<div class="control">
				<input name="project_name" type="text" class="input" value="<?=set_value('project_name', '');?>" required>
			</div>
		</div>
		<div class="field">
			<label class="label">Project Leader: </label>
			<div class="control"><input name="project_leader" type="text" class="input" value="<?=set_value('project_leader', '');?>"></div>
		</div>
		<div class="field">
			<label class="label">Project Description:</label>
			<div class="control">
				<textarea name="project_desc" type="textarea" class="textarea"><?php echo set_value('project_desc'); ?></textarea>
				<p class="has-text-right">Supports some HTML Markups. Click <?php echo anchor('Help/markups', 'here');?> to learn more.</p>
			</div>
		</div>
		<hr>
		<?=form_submit('submit', 'Create', 'class="button is-info is-medium"');?>
	</form>
</section>