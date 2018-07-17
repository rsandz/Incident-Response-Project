<?php /*
DO NOT USE IN VIEW
===================

Copy and Paste this instead, or insert as a string after formatting using
$this->view('templates/errors', $data, TRUE)
*/?>


<!--Errors-->

<?php echo validation_errors('<span class="has-text-danger">', '</span><br>')?>
<span class="has-text-danger"><?php if (isset($errors)) echo $errors;?></span>
