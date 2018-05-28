<body>
	<div class="section">
		<div class="container">
			<h1 class="title">Search Results</h1>
			<?=$table?>

			<div class="level">
				<div class="level-item">
					<?php  if (isset($page_links)) echo $page_links;?>
				</div>
			</div>
		
			<div class="level">
				<div class="level-item">
					<div class="control">
						<?php echo anchor('Search', 'Go Back', 'class="button is-info"'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
