<?php
    $org = Session::get_loggedin_user();
?>

<?php echo view('input/securitytoken'); ?>

<div class='input'>
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
        'trackDirty' => true,        
        'value' => $mission->content
    )); 
?>
</div>

<div class='input'>
    <label><?php echo __("register:sector"); ?><br /></label>
    <?php
        echo view("input/checkboxes",array(
            'name' => 'sector',
            'columns' => 2,
            'options' => OrgSectors::get_options(),
            'value' => $org->get_sectors()));
    ?>
    <?php echo __('register:sector:other_specify') ?> <?php echo view('input/text', array(
    'name' => 'sector_other',
    'js' => 'style="width:200px"'
)) ?>
</div>


<div class='input'>
<label><?php echo __('register:location') ?></label>
<div>
<?php echo __('register:city') ?> <?php echo view('input/text', array(
    'name' => 'city',
    'js' => 'style="width:200px"',
    'value' => $org->city
)) ?>, <?php echo escape($org->get_country_text()); ?>
</div>
<div>
<?php echo __('register:region') ?> <?php echo view('input/pulldown', array(
    'name' => 'region',
    'options' => Geography::get_region_options($org->country),
    'empty_option' => __('register:region:blank'),
    'value' => $org->region
)) ?>
</div>

</div>

<div class='input'>
<label><?php echo __('register:theme') ?></label>
<div class='help'><?php echo __('register:theme:help') ?></div>
</div>

<?php echo view('input/theme', array(
    'name' => 'theme',
    'value' => $org->theme ?: 'green',
    'options' => Theme::available_names(),
    'previewUrl' => $org->get_url()
)); ?>
