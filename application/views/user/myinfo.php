<div class="section">
	<div class="container">
		<h1 class="title">My Information</h1>
		<div class="columns">
			<div class="column">
				<div class="box">
					<h2 class="subtitle">User Information</h2>
					<hr>
					<p><span class="has-text-weight-bold">Name: </span><?php echo $this->session->name?></p>
					<p>
				</div>
			</div>
			<div class="column">
				<div class="box content">
					<h2 class="subtitle">My Teams</h2>
					<hr>
					<ul>
						<?php foreach($user_teams as $index => $team):?>
						<li><?php echo $team->team_name?></li>
						<?php endforeach;?>
					</ul>
				</div>
			</div>
			<div class="column">
				
			</div>
		</div>
		</div>
	</div>
</div>