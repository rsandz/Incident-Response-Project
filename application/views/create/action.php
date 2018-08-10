<?php echo script_tag('js/descriptions.js');?>
<?php echo css_tag('css/bulma-tooltip.min.css')?>

<section class="content section">
	<h1>Action Creation Form</h1>
	<hr>
	<?php echo form_open('Create/index/action', 'class="form"'); ?>
		<div class="field">
			<div class="columns">
				<div class="column">
					<label class="label">For Project: <span class="has-text-danger">(Required)</span></label>
					<div class="control select">
						<?= form_dropdown('project_id', $projects, NULL,'class="init-select2" id="project-selector" required');?>
					</div>
				</div>
				<!-- PROJECT DESCRIPTION -->
				<div class="column">
					<div class="content">
						<p id="project-desc"></p>
					</div>
				</div>
			</div>
		</div>
		<div class="field">
			<label class="label">Action Name: <span class="has-text-danger">(Required)</span></label>
			<div class="control">
				<input name="action_name" class = "input" type="text" value="<?=set_value('action_name')?>" required>
			</div>
		</div>		
		<div class="field">
			<label class="label">Action Type: <span class="has-text-danger">(Required)</span></label>
			<div class="control">
				<div class="select">
					<select class="init-select2" name="action_type" id="type-selector" required>
						<?php foreach ($types as $type): ?>
							<?php echo '<option value="'.$type->type_id.'">'.$type->type_name.'</option><br>' ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="field">
			<label class="label">Action Description</label>
			<div class="control">
				<textarea name="action_desc" class="textarea" value="<?=set_value('action_desc')?>"></textarea>
				<p class="has-text-right">Supports some HTML Markups. Click <?php echo anchor('Help/markups', 'here');?> to learn more.</p>
			</div>
		</div>
		<div class="label field">
			<label class="checkbox"><input name="is_global" type="checkbox" value="1"> 
				Is Global?
				<span style="padding-right: 10px" class="tooltip is-tooltip-right"
                    data-tooltip="Allows Usage Regardless of Project">
                        <i class="far fa-question-circle"></i>
                </span>
			</label>
		</div>
		<hr>
		<?=form_submit('submit', 'Create', 'class="button is-info is-medium"');?>
	</form>
</section>