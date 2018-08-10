<section class="hero is-dark is-fullheight is-bold">
		<div class="hero-head">
			<div class="level">
				<div class="level-left">
					<div class="level-item">
						<img src="<?php echo assets_url('img/logo.png')?>" alt="Incident Response Project"
							width='300' class="logo-pad">
					</div>
				</div>
			</div>
		</div>
		<div class="hero-body">
			<div class="container">
				<div class="columns is-centered">
					<div class="column is-half">
						<h1 class="title has-text-white">Login to your Account</h1>
						<hr>
						<?php echo form_open('login');?>
							<div class="field">
								<label class="label has-text-white" for="email">Email:</label>
								<div class="control has-icons-left">
									<input class="input" type="text" name="email" 
									value="<?php echo set_value('email'); ?>" id='email'>
									<span class="icon is-small is-left">
										<i class="fas fa-envelope"></i>
									</span>
								</div>
							</div>
					
							<div class="field">
								<label class="label has-text-white" for="password">Password:</label>
								<div class="control has-icons-left">
									<input class="input" type="Password" name="password" id='password'>
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
						</form>
						<!--Errors-->
						<?php if (isset($errors)) echo $errors;?>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>