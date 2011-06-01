<div class='input'>
    <label><?php  echo __('widget:section_title'); ?></label><br />
    <?php echo view("input/text", array(
        'name' => 'title',
        'id' => 'title',
        'maxlength' => '60',
        'track_dirty' => true,        
        'value' => @$vars['value']
    )); 
    ?>
</div>
