<?/* Template for Search Boxex (Has link that goe to search) */?>

<div class="box">
    <h2><?php echo $title?></h2>
    <?php echo form_open('Search/result')?>
        <input type="hidden" name="query" value='<?php echo $query?>'>
        <input type="submit" value="Search" class="button is-info">
        <input type="hidden" name="back_url" value="<?php echo current_url()?>">
    </form>
</div>