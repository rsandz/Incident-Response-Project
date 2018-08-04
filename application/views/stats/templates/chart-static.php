<?
    /* 
        Static Chart template for Chart Lib
    */
?>

<div class="chart-wrapper">
    <h2 class="subtitle" style="margin-top : 0"><?php echo $title?><i class="spinner fa fa-spinner fa-pulse fa-fw"></i></h2>
    <canvas class="static-chart" data-chart='<?php echo json_encode($chart_data)?>'></canvas>
</div>