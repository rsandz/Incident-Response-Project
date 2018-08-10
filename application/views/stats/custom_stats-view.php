<section class="section">
	<div class="level">
		<div class="level-left">
			<div class="level-item">
				<h1 class="title">Custom Statistics <?php echo $index?></h1>
			</div>
		</div>
		<div class="level-right">
			<div class="level-item">
				<?php echo anchor('Stats/custom/create/'.$index, 'Edit Query', 'class="button is-primary"');?>
			</div>
		</div>
	</div>
	<hr>
	<h2 class="subtitle">Your Previous Search Query was:</h2>
	<div>
		<?php echo $query_string?>
	</div>
</section>
