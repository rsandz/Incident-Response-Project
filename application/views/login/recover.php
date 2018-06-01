<div class="section">
	<div class="container">
		<div class="columns is-centered">
			<div class="box column is-half">
				<div class="box has-background-info">
					<h2 class="title has-text-white">Password Recovery</h2>
				</div>
				<?php echo form_open('recover');?>
					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Enter your email address</label>
								<div class="control">
									<input class="input <?php if (form_error('email') !== '') {echo 'is-danger';}?>" type="text" name="email">
								</div>
							</div>
						</div>
						<div class="column">
							<div class="content">
								<p>To reset your password, enter your email. You will then be sent a link to reset your password promptly</p>
							</div>
						</div>
					</div>
					<div class="field is-grouped">
						<div class="control">
							<input class="button is-info" type="submit" name="submit">
						</div>
						<div class="control">
							<?php echo anchor('login', 'Cancel', 'class="button is-danger"');?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>