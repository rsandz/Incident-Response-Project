<?
    /* 
        Static Chart template for Chart Lib
    */
?>


<div class="static-chart-wrapper">
    <h2 class="subtitle"><?php echo $title?></h2>
    <canvas class="static-chart" data-chart='<?php echo json_encode($chart_data)?>'></canvas>
</div>