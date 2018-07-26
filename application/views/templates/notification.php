<?php
    /*
        Notification Template
        ---------------------
        Use for when you want to show the user something 
        important. (i.e. Settings Updated)
    */
?>

<?php if (!empty($notification)):?>
    <div class="container">
        <div class="notification <?php echo isset($notification_colour) ? $notification_colour : 'is-primary'?>">
            <i class="delete notify-delete"></i>
            <p class="has-text-weight-bold"><?php echo $notification?></p>
            <p><?php echo isset($notification_body) ? $notification_body : NULL?></p>
        </div>
    </div>
    <script>
        $(function(){
            $('.notify-delete').click(function() 
            {
                $(this).parent().hide();
            });
        })
    </script>
<?php endif;?>