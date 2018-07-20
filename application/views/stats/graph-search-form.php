<!-- Search form for the graph 
		- Runs when a graph element is clicked
	Contains hidden 'back_url' field, which is used for the back button on the search page
-->
<?php echo form_open(site_url('Search/result'), 'id="search-form"', array('back_url' => current_url()));?> 
	<input id="from_date" name="from_date" hidden>
	<input id="to_date" name="to_date" hidden>
	<!-- Project and Team ID for AJAX-->
	<input id="project_id" name="project_id" <?php echo isset($project_id) ? "value='${project_id}'" : NULL;?> hidden>
	<input id="team_id" name="team_id" <?php echo isset($team_id) ? "value='${team_id}'" : NULL;?> hidden>
	<!-- Search Query-->
	<input id="query" name="query" hidden>
	<!-- Search Query Index for Custom Querries -->
	<input id="query_index" name="query_index" <?php echo isset($index) ? "value='$index'" : NULL;?> hidden>
</form>


