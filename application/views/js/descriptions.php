<script type="text/javascript">
	$(function() 
	{
		getActions();
		setProjectInfo();
		$('#project-selector').change(setProjectInfo).change(getActions);
		$('#type-selector').change(getActions);
	});

	function setActionInfo() 
	{
		if ($('#action-selector').val() !== 'NULL')
		{
			$.get(
					"<?php echo site_url('Logging/get_info')?>",
					{	
						action_id : $('#action-selector').val(),  
						table : 'actions'
					},
					function(data) 
					{
						data = $.parseJSON(data);
						if (!data.error) 
						{
							$('#action-desc').html("<strong>Action Description</strong>: </br>"+data.action_desc);
						}
						else
						{
							$('#action-desc').html("<strong>"+ data.error +"</strong>");
							$('#action-type').html("");
						}
					});
		}
		else
		{
			$('#action-desc').html("<strong>No Description</strong>");
		}
	}

	function setProjectInfo() 
	{
		$.get(
				"<?php echo site_url('Logging/get_info')?>",
				{project_id : $('#project-selector').val(), table : 'projects'},
				function(data) 
				{
					data = $.parseJSON(data);
					if (!data.error) 
					{
						$('#project-desc').html("<strong>Project Description</strong>: </br>"+data.project_desc);
					}
					else
					{
						$('#project-desc').html("<strong>"+ data.error +"</strong>");
					}
				});
	}

	function getActions()
	{
		selectedProject_id = $('#project-selector').val();
		$.get(
			"<?php echo site_url('Logging/get_action_items')?>",
			 {
			 	project_id : selectedProject_id,
			  	type_id : $('#type-selector').val()
			 }, 
			 function(data)
			 {
			 	data = $.parseJSON(data);
			 	$('#action-div').html(data);

			 	$('#action-selector').change(setActionInfo); //Re-add the even listener
				setActionInfo();
			 });
	}

</script>
</html>