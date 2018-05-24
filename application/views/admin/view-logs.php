<body>
	
	<div class="section">
	<div class="content">
		<?=$table?>
	</div>
	<div class="container">
		<?=$page_links?>
	</div>
	<div class="container">
		<p><?='Total Entries: '.$total_entries?></p>
	</div>
	</div>

	<div class="section">
		<div class="column is-half is-offset-one-quarter has-text-centered">
			<?=anchor('home', 'Dashboard', 'class="button is-primary"');?>
		</div>
	</div>
	
</body>
