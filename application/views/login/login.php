<body>

<?php echo form_open('login', 'class="pure-form pure-form-aligned"');?>
	
	<h1>Login to your Account</h1><br>

	<?php echo validation_errors() // Displays Errors ?>

	<label>Email:</label>
	<input type="text" name="email" value="<?php echo set_value('email'); ?>"> <br>
	
	<label>Password:</label>
	<input type="Password" name="password"> <br>
	
	<input type="submit" name="submit_btn" class="pure-button" value="Login">

	<?php if (isset($errors)) echo $errors;?>
</form>
</body>
</html>