<div class='input'>
    <label><?php  echo __("widget:title"); ?></label><br />
    <?php echo view("input/text", array(
        'name' => 'title',
        'id' => 'title',
        'maxlength' => '127',
        'track_dirty' => true,        
        'value' => @$vars['value']
    )); 
    ?>
</div>
