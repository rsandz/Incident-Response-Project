<script type="text/javascript">
	//This will fix the fact that the pagination for codeignitor only creates the link in the text and not the button.
	$(function() 
	{
		$('.pagination-link').ready(function() {
			console.log($(self));
			$(this).wrap("<a></a>");
		});
	})

</script>