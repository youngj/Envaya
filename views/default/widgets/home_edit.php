<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    $escUrl = urlencode($_SERVER['REQUEST_URI']);

    ob_start();
?>


<div class='section_header'><?php echo __("header"); ?></div>
<div class='section_content padded'>
    <div class='header_preview'>
        <?php
            if ($org->custom_header)
            {
                echo elgg_view('org/custom_header', array('org' => $org));
            }
            else
            {
                echo elgg_view('org/default_header', array('org' => $org, 'subtitle' => __('header:subtitle')));
            }
        ?>
    </div>
    <strong>
        <a href="<?php echo "{$org->getURL()}/design?from=$escUrl" ?>"><?php echo __('header:edit'); ?></a>
    </strong>
</div>

<div class='section_header'><?php echo __("org:mission"); ?></div>
<div class='section_content padded'>
<div class='input'>    

    <label><?php echo __('setup:mission') ?></label>
    <?php echo elgg_view("input/tinymce", array(
        'internalname' => 'content',
        'trackDirty' => true,
        'valueIsHTML' => $widget->hasDataType(DataType::HTML),
        'value' => $widget->content)) ?>
</div>
<?php echo elgg_view('input/submit', array('internalname' => "submit", 'trackDirty' => true, 'value' => __('savechanges'))); ?>
</div>

<div class='section_header'><?php echo __("org:sectors"); ?></div>
<div class='section_content padded'>
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
    'value' => $org->sector_other,
    'js' => 'style="width:200px"'
)) ?>
</div>
</div>

<div class='section_header'><?php echo __("org:location"); ?></div>
<div class='section_content padded'>

<label><?php echo __('setup:location') ?></label>
<div>
<?php echo __('setup:city') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'city',
    'js' => 'style="width:200px"',
    'trackDirty' => true,
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
<br />
<br />
<?php
    $lat = $org->getLatitude() ?: 0.0;
    $long = $org->getLongitude() ?: 0.0;

    $zoom = ($lat || $long) ? 11 : 1;

    echo elgg_view("org/map", array(
        'lat' => $lat,
        'long' => $long,
        'zoom' => $zoom,
        'pin' => true,
        'org' => $group,
        'edit' => true
    ));
?>

</div>
</div>

<?php
    $content = ob_get_clean();

    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
?>