<?php /*
DO NOT USE IN VIEW
===================

Copy and Paste this instead, or insert as a string after formatting using
$this->view('templates/errors', $data, TRUE)
*/?>


<!--Errors-->
<?php if (!empty(validation_errors()) OR !empty($errors)): ?>
	<div class="column">
		<div class="notification is-danger">
			<?php echo validation_errors()?>
			<?php if (isset($errors)) echo $errors;?>
		</div>
	</div>
<?php endif;?>
