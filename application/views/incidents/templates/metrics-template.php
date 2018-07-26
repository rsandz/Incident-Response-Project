<!-- For Displaying Current Settings-->
<?php foreach($current_settings as $setting):?>
    <div class="field is-grouped ">
            <div class="control select">
                <?php echo form_dropdown('metrics_name[]', $metrics, $setting['metric_name'])?>
            </div>
            <div class="control select">
                <select name="metrics_operator[]" class="operator-select">
                    <option <?php if ($setting['metric_operator'] == '>') echo 'selected'?></option><</option>
                    <option <?php if ($setting['metric_operator'] == '<=') echo 'selected'?>><=</option>
                    <option <?php if ($setting['metric_operator'] == '=') echo 'selected'?>> = </option>
                    <option <?php if ($setting['metric_operator'] == '>=') echo 'selected'?>>>=</option>
                    <option <?php if ($setting['metric_operator'] == '>') echo 'selected'?>> > </option>
                </select>
            </div>
            <div class="control">
                <input name="metrics_value[]" class="input metric-value" 
                    type="number" class="input" required <?php echo "value='{$setting['metric_value']}'"?> >
            </div>
            <div class="control">
                <div class="a button is-danger metric-delete">
                    <div class="span icon is-small">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
<?php endforeach;?>