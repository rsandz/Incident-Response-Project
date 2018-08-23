<?php 
    /* 
        Sort Drop down Template 
        -----------------------

        For use for get_sort_dropdown in search_helper
    */
?>
<form id="sort-form">
    <div class="field is-grouped">
        <div class="control select">
            <?php echo $sort_fields?>
        </div>
        <div class="control select">
            <?php echo $sort_dir?>
        </div>
    </div>
</form>
<!-- Sort Dropdown JS -->
<script>
    $(function()
    {
        $('#sort-fields, #sort-dir').change(function(){

            var data = $('#sort-form').serialize();
            
            $.ajax({
                method: 'GET',
                url: $('#ajax-link').attr('data')+"/set_sort/search",
                data: data,
                dataType: "json",
                success: function (response) {
                    location.reload();
                }
            });

            
        });
    });
</script>

