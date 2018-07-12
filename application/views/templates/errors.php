<!-- 
DO NOT USE IN VIEW
===================

Copy and Paste this instead
-->


<!--Errors-->
<?php echo validation_errors('<span class="has-text-danger">', '</span><br>')?>
<span class="has-text-danger"><?php if (isset($errors)) echo $errors;?></span>
