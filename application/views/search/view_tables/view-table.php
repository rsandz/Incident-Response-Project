<section class="container">
	<h1 class="title">
		<?php echo humanize($table_name).' Table';?>
	</h1>
	<?=$table?>

	<div class="level">
		<div class="level-left">
			<div class="level-item">
				<?php  if (isset($page_links)) echo $page_links;?>
			</div>
		</div>
		<div class="level-right">
			<p><strong>Total Matches:</strong> <?php echo $num_rows?></p>
		</div>
	</div>

	<div class="level">
		<div class="level-item">
			<div class="control">
				<?php echo anchor('Search/view_tables', 'Go Back', 'class="button is-info"'); ?>
			</div>
		</div>
	</div>
</section>
