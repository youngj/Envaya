<div class='input'>
    <label><?php  echo __("widget:title"); ?></label><br />
    <?php echo view("input/text", array(
        'name' => 'title',
        'id' => 'title',
        'js' => "maxlength='127'",
        'trackDirty' => true,        
        'value' => @$vars['value']
    )); 
    ?>
</div>
