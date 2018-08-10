<section class="section">
	<div class="level">
		<div class="level-left">
			<div class="level-item">
				<h1 class="title">My Account Information</h1>
			</div>
		</div>
		<div class="level-right">
			<div class="level-item"><i>Today is: <?php echo date('D, d \of M Y')?></i></div>
		</div>
	</div>
	<hr>
	<div class="columns">
		<div class="column">
			<div class="box content">
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
	</section>
</div>