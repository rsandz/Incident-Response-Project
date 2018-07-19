<!-- Admin Settings -->

<div class="section">
	<div class="container">
		<?php echo form_open('Account/admin-settings');?>
			<h1 class="title">Incident Notification Settings</h2>
			<hr>
			<div class="field">
				<div class="control">
						<input name="hidden" name="notify_new_incident" value="0" hidden> <!--Hidden field for default-->
					<label class="checkbox">
						<input class="checkbox" type="checkbox" name="notify_new_incident" value="1" 
							<?php if ($current_settings->notify_new_incident) echo 'checked'?>>
						Notify on New Incidents
					</label>
				</div>
			</div>
			<div class="field">
				<div class="control">
						<input name="hidden" name="notify_investigated" value="0" hidden> <!--Hidden field for default-->
					<label class="checkbox">
						<input class="checkbox" type="checkbox" name="notify_investigated" value="1" 
							<?php if ($current_settings->notify_investigated) echo 'checked'?>>
						Notify on After Incident Investigation
					</label>
				</div>
			</div>
			<div class="level">
				<div class="level-left">
				</div>
				<div class="level-right">
					<?php echo form_submit('submit', 'Submit', 'class="button is-info"'); ?>
				</div>
			</div>
		</form>
	</div>
</div>

