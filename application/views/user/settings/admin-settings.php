<!-- Admin Settings -->

<section class="section">
	<?php echo form_open('Account/admin-settings');?>
		<h1 class="title">Admin Settings</h2>
		<hr>
		<h2 class="subtitle">Incident Notification Settings</h2>
		<div class="field">
			<div class="control">
					<input name="hidden" name="notify_incident_email" value="0" hidden> <!--Hidden field for default-->
				<label class="checkbox">
					<input class="checkbox" type="checkbox" name="notify_incident_email" value="1" 
						<?php if ($current_settings->notify_incident_email) echo 'checked'?>>
					Notify on New Incidents by Email
				</label>
			</div>
		</div>
		<?php if ($has_phone_num):?>
			<div class="field">
				<div class="control">
						<input name="hidden" name="notify_incident_sms" value="0" hidden> <!--Hidden field for default-->
					<label class="checkbox">
						<input class="checkbox" type="checkbox" name="notify_incident_sms" value="1" 
							<?php if ($current_settings->notify_incident_sms) echo 'checked'?> >
						Notify on New Incidents by SMS
					</label>
				</div>
			</div>
		<?php else:?>
		<!-- If Phone number is not set-->
			<div class="field">
				<div class="control">
					<label class="checkbox" disabled>
						<input type="checkbox"disabled="disabled">
						<span class="is-danger">
							To enabe SMS notification, set your phone number 
							<a href="<?php echo site_url('Account/settings')?>">here</a>
						</span>
					</label>
				</div>
			</div>
		<?php endif;?>
		<div class="level">
			<div class="level-left">
			</div>
			<div class="level-right">
				<?php echo form_submit('submit', 'Submit', 'class="button is-info"'); ?>
			</div>
		</div>
	</form>
</section>

