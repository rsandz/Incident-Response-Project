<section class="content section">
	<h1>Project Creation Form</h1>
	<hr>
	<?php echo form_open('Create/index/project', 'class="form"'); ?>
		<div class="field">
			<label class="label" for="project-name">
				Project Name: <span class="has-text-danger">(Required)</span>
			</label>
			<div class="control">
				<input name="project_name" type="text" class="input" id="project-name"
					value="<?=set_value('project_name', '');?>" required>
			</div>
		</div>
		<div class="field">
			<label class="label" for="project-leader">Project Leader: </label>
			<div class="control">
				<div class="select">
					<?php echo form_dropdown('project_leader', $project_leaders, NULL, 'id="project-leader"');?>
				</div>
			</div>
		</div>
		<div class="field">
			<label class="label" for="project-desc">Project Description:</label>
			<div class="control">
				<textarea name="project_desc" type="textarea" class="textarea" id="project-desc"><?php echo set_value('project_desc'); ?></textarea>
				<p class="has-text-right">Supports some HTML Markups. Click <?php echo anchor('Help/markups', 'here');?> to learn more.</p>
			</div>
		</div>
		<hr>
		<?=form_submit('submit', 'Create', 'class="button is-info is-medium"');?>
	</form>
</section>

<script>
	//Init project leader select
	$(function(){
		$('#project-leader').select2({
			placeholder: 'Select a User..'
		});
	});
</script>