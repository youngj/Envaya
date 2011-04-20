<div class='input'>
    <label><?php  echo __('widget:section_title'); ?></label><br />
    <?php echo view("input/text", array(
        'name' => 'title',
        'id' => 'title',
        'js' => "maxlength='60'",
        'trackDirty' => true,        
        'value' => @$vars['value']
    )); 
    ?>
</div>
