<?php echo script_tag('js/Chart.js')?>
<?php echo script_tag('js/myChart.js')?>
<?php echo script_tag('js/moment.js')?>

<div class="section">
	<div class="container">
        <?php
            foreach($charts as $chart)
            {
                echo $chart;
            }
        ?>
	</div>
</div>
