<body>

	<div class="section">
		<div class="container">
			<div class="columns is-centered">
				<div class="column is-half">
					<div class="box">
						<div class="box has-background-info ">
								<h1 class="title has-text-white">Login to your Account</h1>
						</div>
						<?php echo form_open('login');?>
						<div class="field">
							<label class="label">Email:</label>
							<div class="control has-icons-left">
								<input class="input" type="text" name="email" value="<?php echo set_value('email'); ?>">
								<span class="icon is-small is-left">
							      <i class="fas fa-envelope"></i>
							    </span>
							</div>
						</div>
				
						<div class="field">
							<label class="label">Password:</label>
							<div class="control has-icons-left">
								<input class="input" type="Password" name="password">
								<span class="icon is-small is-left">
									<i class="fas fa-key"></i>
								</span>
							</div>
						</div>
				
				
						<div class="level">
							<div class="level-left">
								<div class="level-item">
									<p id="password_reset"><?php echo anchor('recover', 'Forgot your password?');?></p>
								</div>
							</div>
							<div class="level-right">
								<div class="level-item">
									<div class="field">
										<div class="control">
											<input class="button is-info" type="submit" name="submit_btn" value="Login">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!--Errors-->
					<?php if (isset($errors)) echo $errors;?>

					</form>
				</div>
			</div>
		</div>
	</div>
		
	
</body>
</html>