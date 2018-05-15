<body>
	<div class="hero is-info">
		<div class="hero-body">
			<h1 class="title">View All Logs</h1>
		</div>
	</div>
	
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
