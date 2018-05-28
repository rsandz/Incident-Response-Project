<div class="content section">
	<?php echo form_open('Create/index/team', 'class="form"'); ?>
		<div class="field">
			<label class="label">Team Name</label>
			<div class="control"><input name="team_name" type="text" class="input" value="<?=set_value('team_name')?>"></div>
		</div>
		<div class="field">
			<label class="label">Team Leader</label>
			<div class="control"><input name="team_leader" type="text" class="input" value="<?=set_value('team_leader')?>"></div>
		</div>
		<div class="field">
			<label class="label">Team Description</label>
			<div class="control"><input name="team_desc" type="textarea" class="input" value="<?=set_value('team_desc')?>"></div>
		</div>
		<?=form_submit('submit', 'Create', 'class="button is-primary is-medium"');?>
	</form>
</div>