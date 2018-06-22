<body>
	<div class="section">
		<div class="container">
			<h1 class="title">
				Search Results <?php if (count($query['keywords']) > 0) {echo 'for: '.implode(' ',$query['keywords']);}?>
			</h1>
			<?=$table?>

			<div class="level">
				<div class="level-left">
					<div class="level-item">
						<?php  if (isset($page_links)) echo $page_links;?>
					</div>
				</div>
				<div class="level-right">
					<p><strong>Total Matches:</strong> <?php if (isset($num_rows)) echo $num_rows?></p>
				</div>
			</div>
		
			<div class="level">
				<div class="level-item">
					<div class="control">
						<?php if (isset($type) && $type == 'graph'):?>
							<?php echo anchor('stats/my_stats', 'Go Back', 'class="button is-info"'); ?>
						<?php else: ?>
							<?php echo anchor('Search', 'Go Back', 'class="button is-info"'); ?>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
