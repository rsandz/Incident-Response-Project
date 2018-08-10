<!-- Load Tooltips -->
<?php echo css_tag('css/bulma-tooltip.min.css')?>
<section class="content section">
	<h1>Action Type Creation Form</h1>
	<hr>
	<?php echo form_open('Create/index/action_type', 'class="form"'); ?>
		<div class="field">
			<div class="control">
				<label for="" class="label">Action Type Name: <span class="has-text-danger">(Required)</span></label>
				<input class="input" type="text" name="action_type_name" value="<?php echo set_value('action_type_name')?>" required>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label for="" class="label">Action Type Description</label>
				<textarea class="textarea"type="text"  name="action_type_desc"><?php echo set_value('action_type_desc')?></textarea>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input class="checkbox" name="is_active" type="checkbox" value="1" 
					<?php echo set_checkbox('is_active', 1, TRUE); ?> >
					Is Active? 
					<span style="padding-right: 10px" class="tooltip is-tooltip-right"
                    data-tooltip="Unchecking this prevent users from using this to make logs.">
                        <i class="far fa-question-circle"></i>
                    </span>
				</label>
			</div>
		</div>
		<hr>
		<?=form_submit('submit', 'Create', 'class="button is-primary is-medium"');?>
	</form>
</section>