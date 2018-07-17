<div class="content section">
	<div class="container">
		<h1>Team Creation Form</h1>
		<hr>
		<?php echo form_open('Create/index/team', 'class="form"'); ?>
			<div class="field">
				<label class="label">Team Name: <span class="has-text-danger">(Required)</span></label>
				<div class="control"><input name="team_name" type="text" class="input" value="<?=set_value('team_name')?>"></div>
			</div>
			<div class="field">
				<label class="label">Team Leader: </label>
				<div class="control"><div class="select"><?php echo $team_leaders_select?></div></div>
			</div>
			<div class="field">
				<label class="label">Team Description:</label>
				<div class="control">
					<textarea name="team_desc" type="textarea" class="textarea" value="<?=set_value('team_desc')?>"></textarea>
					<p class="has-text-right">Supports some HTML Markups. Click <?php echo anchor('Help/markups', 'here');?> to learn more.</p>
				</div>
			</div>
			<?=form_submit('submit', 'Create', 'class="button is-primary is-medium"');?>
		</form>
	</div>
</div>