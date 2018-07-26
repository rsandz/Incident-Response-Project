<div class="section">
    <div class="container">
        <h1 class="title is-2">Google Analytics Settings</h1>  
        <hr>
        <?php echo form_open()?>
            <div class="field">
                <div class="control">
                    <label class="label">
                        View ID:
                    </label>
                    <input class="input" name="view_id" type="text">
                </div>
            </div>
            <hr>
            <h3 class="title is-3">Metrics to Monitor</h3>
            <div class="field is-grouped">
                <div class="control">
                    <label class="label is-medium" >
                        <input class="metric-enable" type="checkbox">
                        Example Metric
                    </label>
                </div>
                <div class="control select">
                    <select name="operator[]" class="operator-select">
                        <option><</option>
                        <option><=</option>
                        <option>=</option>
                        <option>>=</option>
                        <option>></option>
                    </select>
                </div>
                <div class="control"><input class="input metric-value" type="text" class="input"></div>
            </div>
            <hr>
            <div class="level">
                <div class="level-left">
                    <div class="level-item">
                        <?php echo anchor('Incidents', 'Back', 'class="button is-danger is-medium"')?>
                    </div>
                    <div class="level-item">
                        <?php echo form_submit('submit', 'Update', 'class="button is-info is-medium"')?>
                    </div>
                </div>
                <div class="level-right"></div>
            </div>
        </form>
    </div>
</div>

<script>
    $('.metric-enable').change(toggle_field)

    /**     
    *   Toggle the input and select elements in a field.
    */
    function toggle_field(event)
    {
        let parentField = $(this).parentsUntil('form').last();
        let fieldInputs = parentField.find('select,.metric-value');

        if ($(this).is(':checked'))
        {
            fieldInputs.attr('disabled', true);
        }
        else
        {
            fieldInputs.attr('disabled', false);
        }
    }
</script>