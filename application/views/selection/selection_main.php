<section class="section is-top-marginless">
	<div class="content">
		<div class="level">
			<div class="level-left">
				<div class="level-item">
					<h2 class="subtitle"><?php echo $title?></h2>
				</div>
			</div>
			<div class="level-right">
				<div class="level-item">
					<!-- Sort Dropbox -->
					<?php if (isset($sort_options)): ?>
						<form id="sort-form">
							<div class="field is-grouped">
								<div class="control select">
									<?php echo form_dropdown('sort_field', $sort_options,
										$current_sort['sort_field'], 'id="sort-fields"'); ?>
								</div>
								<div class="control select">
									<select id="sort-dir" name='sort_dir'>
										<?php $sort_dir = $current_sort['sort_dir']?>
										<option value="asc" <?php if($sort_dir == 'asc') echo 'selected'?>>Ascending</option>
										<option value="desc" <?php if($sort_dir == 'desc') echo 'selected'?>>Descending</option>
									</select>
								</div>
							</div>
						</form>
					<?php endif; ?>
				</div>
				<div class="level-item">
					<div class="control has-icons-left">
						<input id="selection-search" class="input" type="text" name="Search" placeholder="Search">
						<span class="icon is-small is-left">
						      <i class="fas fa-search"></i>
						</span>
					</div>
				</div>
				<div class="level-item">
					<div class="control">
						<div class="select">
							<select id="select-display" class="font-awesome">
								<option value="bars">Bars</option>
								<option value="grid">Grid</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<?php if(isset($selection_items) && is_array($selection_items) && count($selection_items) > 0):?>
			<div class="columns is-multiline">
				<?php foreach($selection_items as $index => $data):?>
					<div class="column is-12 selection-item">
						<article class="box is-fullheight selection-box">
							<div class="selection-body">
								<h2 class="title is-4">
									<span class="selection-title"><?php echo $data['title']?></span>
								</h2>
								<hr>
								<div class="content selection-info">
									<?php echo $data['body']?>
								</div>
							</div>
							<div class="selection-footer level">
								<div class="level-left">
								</div>
								<div class="level-right">
									<div class="level-item">
										<?php echo anchor($data['link'], $data['link_name'], 'class="button is-info"');?>
									</div>
								</div>
							</div>
						</article>
					</div>
				<?php endforeach;?>
			</div>
		<?php else:?>
			<!-- If no selection data-->
			<?php echo $empty_data_msg?>
		<?php endif;?>
	</div>
</section>

<?php echo script_tag('js/selection.js')?>

<!-- Sort Dropdown JS -->
<script>
    $(function()
    {
        $('#sort-fields, #sort-dir').change(function(){

            var data = $('#sort-form').serialize();
            $.ajax({
                method: 'GET',
                url: $('#ajax-link').attr('data')+"/set_sort/<?php echo $sort_identifier?>",
                data: data,
                success: function (response) {
					console.log(response);
					location.reload();
                }
            });

            
        });
    });
</script>