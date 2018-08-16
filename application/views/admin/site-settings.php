<section class="section">
    <h1 class="title">Site Settings</h1>
    <hr>
    <?php echo form_open('Admin/site-settings')?>
    <div class="field">
        <div class="control">
            <label for="site-notification">
                Site Notification:  
                <span style="padding-right: 10px" class="tooltip is-tooltip-right"
                    data-tooltip="Global Notification Showed on the Dashboard">
                    <i class="far fa-question-circle"></i>
                </span>
            </label>
                <!-- Can't split textarea into multiple lines. Sorry :( -->
                <textarea class="textarea" name="site_notification" id="site-notification" cols="10" rows="5"><?php echo $current_settings['site_notification']?></textarea>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <input type="submit" value="Save" class="button is-info is-medium" name="submit">
            </div>
        </div>
    </form>
</section>

<!--Load Tooltips -->
<?php $this->load->helper('html');?>
<?php echo link_tag('assets/css/bulma-tooltip.min.css')?>