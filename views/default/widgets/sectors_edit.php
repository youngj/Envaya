<div class='section_content padded'>
<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_user();
    
    ob_start();
?>
<div class='input'>
    <label><?php echo __("register:sector"); ?><br /></label>
    <?php
        echo view("input/checkboxes",array(
            'name' => 'sector',
            'columns' => 2,
            'options' => OrgSectors::get_options(),
            'value' => $org->get_sectors()));
    ?>
    <?php echo __('register:sector:other_specify') ?> 
    <?php echo view('input/text', array(
    'name' => 'sector_other',
    'value' => $org->get_metadata('sector_other'),
    'style' => 'width:200px'
)) ?>
</div>
<?php
    $content = ob_get_clean();
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content,
    ));    
?>

</div>
