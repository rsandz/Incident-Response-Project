<body>

	<div class="column is-half is-offset-one-quarter">
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

			<div class="field">
				<div class="control">
					<input class="button is-primary" type="submit" name="submit_btn" class="pure-button" value="Login">

				</div>
			</div>

			<!-- Displays Errors -->
			<?php echo validation_errors('<span class="has-text-danger">', '</span><br>')?>
			<span class="has-text-danger"><?php if (isset($errors)) echo $errors;?></span>

		</div>
	</div>
		
	</form>
</body>
</html>