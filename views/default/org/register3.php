<?php echo view("org/registerProgress", array('current' => 3   )) ?>

<?php
    $org = get_loggedin_user();
?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('setup:instructions') ?>
</div>

<form action='org/register3' method='POST'>

<?php echo view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('setup:mission') ?></label>
<div class='help'>
<?php echo __('setup:mission:help') ?>
</div>
<?php 
    $homeWidget = $org->getWidgetByName('home');
    echo view('input/tinymce', array(
        'internalname' => 'mission',
        'trackDirty' => true,
        'valueIsHTML' => $homeWidget->hasDataType(DataType::HTML),
        'value' => $homeWidget->content
    )); 
?>
</div>


<div class='input'>
<label><?php echo __('setup:language') ?></label><br />
<?php echo view('input/language', array(
    'internalname' => 'content_language',
    'value' => $org->language
)) ?>
<div class='help'>
<?php echo __('setup:language:help') ?>
</div>
</div>

<div class='input'>
    <label><?php echo __("setup:sector"); ?><br /></label>
    <?php
        echo view("input/checkboxes",array(
            'internalname' => 'sector',
            'options' => Organization::getSectorOptions(),
            'value' => $org->getSectors()));
    ?>
    <?php echo __('setup:sector:other_specify') ?> <?php echo view('input/text', array(
    'internalname' => 'sector_other',
    'js' => 'style="width:200px"'
)) ?>
</div>


<div class='input'>
<label><?php echo __('setup:location') ?></label>
<div>
<?php echo __('setup:city') ?> <?php echo view('input/text', array(
    'internalname' => 'city',
    'js' => 'style="width:200px"',
    'value' => $org->city
)) ?>, <?php echo escape($org->getCountryText()); ?>
</div>
<div>
<?php echo __('setup:region') ?> <?php echo view('input/pulldown', array(
    'internalname' => 'region',
    'options_values' => regions_in_country($org->country),
    'empty_option' => __('setup:region:blank'),
    'value' => $org->region
)) ?>
</div>

</div>

<div class='input'>
<label><?php echo __('setup:theme') ?></label>
<div class='help'><?php echo __('setup:theme:help') ?></div>
</div>

<?php echo view('input/theme', array(
    'internalname' => 'theme',
    'value' => $org->theme ?: 'green',
    'options' => $org->getAvailableThemes(),
    'previewUrl' => $org->getURL()
)); ?>


<div class='input'>
<label><?php echo __('setup:next') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('setup:next:button'),
    'trackDirty' => true
));
?>
</div>

</form>

</div>