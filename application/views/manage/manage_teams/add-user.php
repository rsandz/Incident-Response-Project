<div class="section">
	<div class="container">
		<h1 class="title">Select which users to add:</h1>
		<!--User Selection--> 
		<h3>Users:</h3>
		<?php echo form_open('manage_teams/add_users/'.$team_id);?>
			<div class="control">
				<input class="input" type="text" name="user_search" id="user_search" placeholder="Search for Specific User" style="width:40%">
			</div>
			<div class="column" id="selectedUsers">
			</div>
			<hr style="margin : 1px">
			<div class="column" id="users">
				<?php foreach ($users as $user):?>
					<span class="tag button is-light unselectedUser is-medium" style="margin : 2px" data="<?php echo $user->user_id?>"><?=$user->name?></span>
				<?php endforeach; ?>
			</div>
			<hr>
			<div class="level">
				<div class="level-item">
					<input type="Submit" name="submit" value="Add Users" class="button is-info">
				</div>
			</div>
		</form>
</div>

<script type="text/javascript">
	$(function() 
	{	
		//For Filter Box
		$('#filterBtn').click(function() 
		{	
			$('#filterBox').slideToggle('fast');
		});
	
		//User Selection
		$('.unselectedUser').click(function()
		{	
			if ($(this).hasClass('unselectedUser'))
			{
				$(this).appendTo('#selectedUsers');
				
				//Add the input so that it gets into Post Array
				var inputStr = '<input name ="users[]" id="'+ $(this).html() +'_select" value="'+ $(this).attr('data') +'" hidden>';
				$(inputStr).appendTo('#selectedUsers');
	
				//Add Delete button
				$(this).append('<span class="delete"></span>');
	
				$(this).toggleClass('unselectedUser selectedUser is-info is-light');
			}
			else
			{
				$(this).appendTo('#users');
	
				//Remove from post array
				inputId = '#'+$(this).text() +'_select';
				$(inputId).remove();
	
				//Remove Delete button
				$(this).children('.delete').remove();
	
				$(this).toggleClass('unselectedUser selectedUser is-info is-light');
			}
		});
	
		//User Search
	
		$('#user_search').keyup(function()
		{
			$('.unselectedUser').each(function(){
				var user_keyword = $('#user_search').val();
				if ($(this).html().search(new RegExp(user_keyword,'i')) == -1)
				{
					$(this).hide();
				}
				else
				{
					$(this).show();
				}
			});
		});
	
		//Delete Function
	
		$('.delete:not(.tag)').click(function()
		{
			$(this).parent().remove();
		});
		
	});

</script>