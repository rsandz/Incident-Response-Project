<?php echo script_tag('js/Chart.js')?>
<?php echo script_tag('js/myChart.js')?>
<?php echo script_tag('js/moment.js')?>
<section class="section">
    <?php
        foreach($charts as $chart)
        {
            echo $chart;
        }
    ?>
</section>
