<section class="section">
    <h1 class="title">Account Settings</h1>
    <hr>
    <h2 class="subtitle">My Information:</h2>
    <?php echo form_open('Account/save-user-info')?>
        <label class="label">Name</label>
        <div class="field is-grouped">
            <div class="control">
                <input type="text" class="input" placeholder="First Name" 
                    name="first_name" value="<?php echo $current_info->first_name?>" required>
            </div>
            <div class="control">
                <input type="text" class="input" placeholder="Last Name" 
                    name="last_name" value="<?php echo $current_info->last_name?>">
            </div>
        </div>
        <label for="" class="label">Phone Number:</label>
        <div class="field">
            <div class="control">
                <input type="tel" class="phone input" 
                    name="phone_num" value="<?php echo $current_info->phone_num?>">
            </div>
        </div>
        <label for="" class="label">About You:</label>
        <div class="field">
            <div class="control">
                <textarea class="textarea" name="user_desc" id="user_desc"><?php echo $current_info->user_desc?></textarea>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <input class="button is-info" type="submit" value="Save" id="user-info-submit">
            </div>
        </div>
    </form>
    <hr>
    <h2 class="subtitle">Change Your Password:</h2>
    <?php echo form_open('Account/set-password')?>
        <label for="current_pass" class="label">Current Password</label>
        <div class="field has-addons">
            <div class="control">
                <input class="input" type="password" id="current_pass" name="current_pass" required>
            </div>
            <div class="control">
                <p class="button toggle-password">
                    <span class="icon is-small"><i class="fas fa-eye"></i></span>
                </p>
            </div>
        </div>
        <label for="new_pass" class="label">New Password</label>
        <div class="field has-addons">
            <div class="control">
                <input class="input" type="password" id="new_pass" name="new_pass" required>
            </div>
            <div class="control">
                <p class="button toggle-password">
                    <span class="icon is-small"><i class="fas fa-eye"></i></span>
                </p>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <input type="submit" class="button is-info" value="Change">
            </div>
        </div>
    </form>
</section>

<!-- Telephone Plugin -->
<?php echo css_tag('css/intlTelInput.min.css')?>
<?php echo script_tag('js/intlTelInput.min.js')?>
<?php echo script_tag('js/utils.js')?>

<script>
	$(function(){
		//JS to show password if hovering on eye
		$('.toggle-password').mouseenter(function() {
            var field_wrapper = $(this).closest('.field');
            field_wrapper.find('[type=password]').prop('type', 'text');
        });
		$('.toggle-password').mouseleave(function() {
            var field_wrapper = $(this).closest('.field');
            field_wrapper.find('[type=text]').prop('type', 'password');
        });

        //Init telephone input
        $('.phone').intlTelInput({
            allowDropdown: false,
            formatOnDisplay: false
        });
        //Initial Formatting
        $(this).val($(this).intlTelInput("getNumber", intlTelInputUtils.numberFormat.E164));

        //Validate and formatting for phone number
        $('.phone').change(function(){
                //Formatting
                $(this).val($(this).intlTelInput("getNumber", intlTelInputUtils.numberFormat.E164));

                //Check Valid
                if (!$(this).intlTelInput("isValidNumber") && $(this).val() != '') {
                    $(this).addClass('is-danger');
                    $('#user-info-submit').prop('disabled', 'disabled');
                }
                else {
                    $(this).removeClass('is-danger');
                    $('#user-info-submit').prop('disabled', false);
                }
            }
        );
	});
</script>