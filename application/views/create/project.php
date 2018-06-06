<div class="content section">
	<div class="container">
		<?php echo form_open('Create/index/project', 'class="form"'); ?>
			<div class="field">
				<label class="label">Project Name</label>
				<div class="control"><input name="project_name" type="text" class="input" value="<?=set_value('project_name')?>"></div>
			</div>
			<div class="field">
				<label class="label">Project Leader</label>
				<div class="control"><input name="project_leader" type="text" class="input" value="<?=set_value('project_leader')?>"></div>
			</div>
			<div class="field">
				<label class="label">Project Description</label>
				<div class="control"><input name="project_desc" type="textarea" class="input" value="<?=set_value('project_desc')?>"></div>
			</div>
			<?=form_submit('submit', 'Create', 'class="button is-primary is-medium"');?>
		</form>
	</div>
</div>