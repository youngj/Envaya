<?php
    $org = Session::get_logged_in_user();
?>

<?php echo view('input/securitytoken'); ?>

<div class='input'  style='padding-bottom:12px'>
<label><?php echo __('widget:mission:label') ?></label>
<div class='help'>
<?php echo __('register:mission:help') ?>
</div>
<?php 
    $home = $org->get_widget_by_class('Home');    
    $mission = $home->get_widget_by_class('Mission');
    
    echo view('input/tinymce', array(
        'name' => 'mission',
        'autoFocus' => true,
        'track_dirty' => true,        
        'value' => $mission->content
    )); 
?>
</div>

<div class='input'  style='padding-bottom:12px'>
    <label><?php echo __("register:sector"); ?><br /></label>
    <?php
        echo view("input/checkboxes",array(
            'name' => 'sector',
            'columns' => 2,
            'options' => OrgSectors::get_options(),
            'value' => $org->get_sectors()));
    ?>
    <div style='text-align:right'>
    <?php echo __('register:sector:other_specify') ?> <?php echo view('input/text', array(
    'name' => 'sector_other',
    'style' => 'width:200px'
    )); ?>
    </div>
</div>

<div class='input' style='padding-bottom:12px'>
<label><?php echo __('register:theme') ?></label>
<div class='help'><?php echo __('register:theme:help') ?></div>

<?php echo view('input/theme', array(
    'name' => 'theme',
    'value' =>  $org->get_design_setting('theme_name') ?: Config::get('fallback_theme'),
    'previewUrl' => $org->get_url()
)); ?>
</div>

