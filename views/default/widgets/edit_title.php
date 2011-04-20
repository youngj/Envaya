<div class='input'>
    <label><?php  echo __("widget:title"); ?></label><br />
    <?php echo view("input/text", array(
        'name' => 'title',
        'id' => 'title',
        'js' => "style='width:170px' maxlength='22'",
        'trackDirty' => true,        
        'value' => @$vars['value']
    )); 
    
    if (!@$vars['value'])
    {
        echo "<div class='help'>".__('widget:title_help')."</div>";
    }
    ?>
</div>
