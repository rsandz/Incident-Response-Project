<div class="section">
	<div class="container">
		<div class="columns is-centered">
			<div class="column is-half box">
				<div class="content">
					<div class="box has-background-info">
						<h1 class="Title has-text-white">Reset Your Password</h1>
					</div>
					<?php echo form_open('recover-form/'.$user_id.'/'.$email_code);?>

					<div hidden>
						<input type="text" name="reset_hash" hidden value="<?php echo $reset_hash?>">
					</div>
				
					<div class="field">
						<label for="" class="label">Password</label>
						<div class="control">
							<input class="input" type="password" name="password">
						</div>
					</div>
					<div class="field">
						<label for="" class="label">Confirm Password</label>
						<div class="control">
							<input class="input" type="password" name="password_confirm">
						</div>
					</div>
					<div class="field">
						<div class><input class="button is-primary" type="submit"></div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>