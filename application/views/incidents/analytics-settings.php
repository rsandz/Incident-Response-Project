<section class="section is-small">
    <div class="container">
        <h1 class="title is-2">Google Analytics Settings</h1>  
        <hr>
        <?php echo form_open()?>
            <div class="field">
                <div class="control">
                    <label class="label">
                        View ID:
                        <span style="padding-right: 10px" class="tooltip is-tooltip-right"
                        data-tooltip="The View ID from your Google Anlytics Property">
                            <i class="far fa-question-circle"></i>
                        </span>
                    </label>
                    <input class="input" name="view_id" type="text" <?php echo "value='{$view_id}'"?>>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <label class="label">
                        Authentication File Path:
                        <span style="padding-right: 10px" class="tooltip is-tooltip-right is-tooltip-multiline"
                        data-tooltip="The Authentication file from your Google Analytics Service Account. 
                        We Recomend to change this in the config file so you have access to `APPPATH`">
                            <i class="far fa-question-circle"></i>
                        </span>
                    </label>
                    <input class="input" name="auth_path" type="text" <?php echo "value='{$auth_path}'"?>>
                </div>
            </div>
            <hr>
            <h3 class="title is-3">
                Metrics to Monitor
                <span class="tooltip is-tooltip-right"
                        data-tooltip="Sets conditions that will cause an automatic Incident Creation">
                    <i class="far fa-question-circle"></i>
                </span>
            </h3>
            <div id="metrics-wrapper" class="field">
            <!-- Current Settings -->
                <?php echo $current_settings?>
            </div>  
            <div class="field">
                <div class="control">
                    <p class="button" id="new-metric">
                        <span class="icon is-small">
                            <i class="far fa-plus-square"></i>
                        </span>
                        <span>Add a Metric</span>
                    </a>
                </div>
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
</section>
<!--Load Tooltips-->
<?php $this->load->helper('html');?>
<?php echo link_tag('assets/css/bulma-tooltip.min.css')?>
<!-- Template for JS when adding new metric to track -->
<template id="metrics-template">
    <div class="field is-grouped ">
        <div class="control select">
            <?php echo form_dropdown('metrics_name[]', $metrics)?>
        </div>
        <div class="control select">
            <select name="metrics_operator[]" class="operator-select">
                <option><</option>
                <option><=</option>
                <option>=</option>
                <option>>=</option>
                <option>></option>
            </select>
        </div>
        <div class="control">
            <input name="metrics_value[]" class="input metric-value" type="number" class="input" required>
        </div>
        <div class="control">
            <div class="a button is-danger metric-delete">
                <div class="span icon is-small">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    //Function for making the metric creation gui work
    $(function() {
        //On Page load
        $('.metric-delete').click(metricDelete);
        $('#new-metric').click(newMetric);

    });

    /**
     * Delete a metric condition that the clicked element was 
     * wrapped in
     */
    function metricDelete()
    {
        let parentField = $(this).parentsUntil('#metrics-wrapper').last();
        parentField.remove();
    }

    /**
     * Adds a new metric condition to the metrics wrapper
     */
    function newMetric()
    {
        console.log('click');
        
        let template = $('#metrics-template').html();
        let field = $('#metrics-wrapper').append(template);

        //Add the metric delete listener
        field.find('.metric-delete').click(metricDelete);
        
    }

</script>