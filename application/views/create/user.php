<div class="content section">
	<?php echo form_open('Create/index/user', 'class="form"'); ?>
		<div class="field">
			<label class="label">Name</label>
			<div class="control"><input name="name" type="text" class="input" value="<?=set_value('name')?>"></div>
		</div>
		<div class="field">
			<label class="label">Email (Used for Login)</label>
			<div class="control"><input name="email" type="text" class="input" value="<?=set_value('email')?>"></div>
		</div>
		<div class="field">
			<label class="label">Password</label>
			<div class="control"><input name="password" type="password" class="input"></div>
		</div>
		<div class="field">
			<label class="label">Confirm Password</label>
			<div class="control"><input name="password-confirm" type="password" class="input"></div>
		</div>

		<?=form_submit('submit', 'Create', 'class="button is-primary is-medium"');?>
	</form>
</div>