<div class="content section">
	<div class="container">
		<h1>User Creation Form</h1>
		<hr>
		<?=$errors?>
		<?php echo form_open('Create/index/user', 'class="form"'); ?>
			<div class="field">
				<label class="label">Name: <span class="has-text-danger">(Required)</span></label>
				<div class="field is-horizontal">
					<div class="field-body">
						<div class="field">
							<div class="control">
								<input name="first_name" type="text" class="input" value="<?=set_value('name')?>" placeholder="First Name">
							</div>
						</div>
						<div class="field">
							<div class="control">
								<input name="last_name" type="text" class="input" value="<?=set_value('name')?>" placeholder="Last Name">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="field">
				<label class="label">Email - Used for Login: <span class="has-text-danger">(Required)</span></label>
				<div class="control"><input name="email" type="text" class="input" value="<?=set_value('email')?>"></div>
			</div>
			<div class="field">
				<label class="label">Password: <span class="has-text-danger">(Required)</span></label>
				<div class="control"><input name="password" type="password" class="input"></div>
			</div>
			<div class="field">
				<label class="label">Confirm Password: <span class="has-text-danger">(Required)</span></label>
				<div class="control"><input name="password-confirm" type="password" class="input"></div>
			</div>
			<hr>
			<?=form_submit('submit', 'Create', 'class="button is-info is-medium"');?>
		</form>
	</div>
</div>