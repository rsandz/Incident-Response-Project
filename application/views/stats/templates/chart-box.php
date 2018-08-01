<div class="chart-box">
    <h3 class="title is-3">
        <?php echo $title;?>
        <i class="spinner fa fa-spinner fa-pulse fa-fw"></i>
    </h3>
    <hr>
    <div class="columns chart-view">
        <div class="column">
            <div class="level" style="height:100%">
                <div class="level-item is-narrow">
                    <button class="button chart-left"><</button>
                </div>
                <div class="chart-container level-item" style="min-width: 40%">
                    <canvas class='dynamic-chart' data-ajaxurl="<?php echo $ajax_url?>"></canvas>
                </div>
                <div class="level-item is-narrow">
                    <button class="button chart-right">></button>
                </div>
            </div>
        </div>
        <div class="column is-narrow">
            <nav class="panel">
                <p class="panel-heading">
                    Chart Controls
                </p>
                <div class="panel-block">
                    <div class="level">
                        <div class="level-left">
                            <label class="label" style="margin-right: 5px">Date Interval: </label>
                        </div>
                        <div class="level-right">
                            <p class="control"><?php echo form_dropdown('interval_type', $interval_options, 'daily', 'class="select interval-select"'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="panel-block">
                    <div class="control">
                        <label for="" class="label">Jump to:</label>
                        <div class="field has-addons">
                            <div class="control">
                                <input type="date" class="input jump-date" value="">
                            </div>
                            <div class="control">
                                <p class="button jump-button">Jump</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-block">
                    <div class="control">
                        <label for="" class="label">Number of Datapoints:</label>
                        <div class="field has-addons">
                            <div class="control">
                                <input type="number" name="limit-num" class="input limit-num" value="10">
                            </div>
                            <div class="control">
                                <p class="button limit-button">Set</p>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>