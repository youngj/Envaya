<?php echo elgg_view("org/registerProgress", array('current' => 3   )) ?>

<?php
    $org = get_loggedin_user();
?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('setup:instructions') ?>
</div>

<form action='org/register3' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('setup:mission') ?></label>
<div class='help'>
<?php echo __('setup:mission:help') ?>
</div>
<?php 
    $homeWidget = $org->getWidgetByName('home');
    echo elgg_view('input/tinymce', array(
        'internalname' => 'mission',
        'trackDirty' => true,
        'valueIsHTML' => $homeWidget->hasDataType(DataType::HTML),
        'value' => $homeWidget->content
    )); 
?>
</div>


<div class='input'>
<label><?php echo __('setup:language') ?></label><br />
<?php echo elgg_view('input/language', array(
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
        echo elgg_view("input/checkboxes",array(
            'internalname' => 'sector',
            'options' => Organization::getSectorOptions(),
            'value' => $org->getSectors()));
    ?>
    <?php echo __('setup:sector:other_specify') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'sector_other',
    'js' => 'style="width:200px"'
)) ?>
</div>


<div class='input'>
<label><?php echo __('setup:location') ?></label>
<div>
<?php echo __('setup:city') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'city',
    'js' => 'style="width:200px"',
    'value' => $org->city
)) ?>, <?php echo escape($org->getCountryText()); ?>
</div>
<div>
<?php echo __('setup:region') ?> <?php echo elgg_view('input/pulldown', array(
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

<?php echo elgg_view('input/theme', array(
    'internalname' => 'theme',
    'value' => $org->theme ?: 'green',
    'options' => $org->getAvailableThemes(),
    'previewUrl' => $org->getURL()
)); ?>


<div class='input'>
<label><?php echo __('setup:next') ?></label>
<br />
<?php echo elgg_view('input/submit',array(
    'value' => __('setup:next:button'),
    'trackDirty' => true
));
?>
</div>

</form>

</div>