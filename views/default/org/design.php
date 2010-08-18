<?php
    $user = $vars['entity'];
?>

<form action='<?php echo $user->getURL() ?>/design/save' method='POST'>

<?php echo view('input/securitytoken'); ?>

<div class='section_header' id='icon'><?php echo __('icon'); ?></div>
<div class='section_content padded'>
<div class='help' style='padding-bottom:5px'>
<?php echo __('icon:description') ?>
</div>


<?php

echo view("input/image",
    array(
        'current' => $user->getIcon('medium'),
        'trackDirty' => true,
        'sizes' => User::getIconSizes(),
        'removable' => $user->custom_icon,
        'thumbnail_size' => 'medium',
        'internalname' => 'icon',
        'deletename' => 'deleteicon',
    ))

?>

<?php
echo view('input/submit',array(
    'value' => __('savechanges'),
    'trackDirty' => true,
));

?>

</div>

<div class='section_header'><?php echo __('header'); ?></div>
<div class='section_content padded'>

<script type='text/javascript'>

function getSelectedRadio($name)
{
    var $buttons = document.getElementsByName($name);
    for (var $i = 0; $i < $buttons.length; $i++)
    {
        var $button = $buttons[$i];
        if ($button.checked)
        {
            return $button.value;
        }
    }
    return $null;
}

function customHeaderChanged()
{
    setTimeout(function() {
        var $customDiv = document.getElementById('custom_header_container');
        var $defaultDiv = document.getElementById('default_header_container');
        var $value = getSelectedRadio('custom_header');

        if ($value == '1')
        {
            $customDiv.style.display = 'block';
            $defaultDiv.style.display = 'none';
        }
        else
        {
            $customDiv.style.display = 'none';
            $defaultDiv.style.display = 'block';
        }
    }, 1);
}
</script>

<?php
    echo view('input/radio', array(
        'internalname' => 'custom_header',
        'value' => $user->custom_header ? '1' : '0',
        'js' => "onchange='customHeaderChanged()' onclick='customHeaderChanged()'",
        'options' => array(
            '0' => __('header:default'),
            '1' => __('header:custom'),
        )
    ));
?>

<div id='default_header_container' <?php echo $user->custom_header ? "style='display:none'" : "" ?> >
    <div class='header_preview'>
        <?php echo view('org/default_header', array('org' => $user, 'subtitle' => __('header:subtitle'))) ?>
    </div>
    <div class='help'><?php echo sprintf(__('header:changelogo'), __('icon')) ?></div>
</div>

<div id='custom_header_container' <?php echo !$user->custom_header ? "style='display:none'" : "" ?>>

    <?php
        if ($user->custom_header)
        {
            echo "<div style='margin-top:10px'>".__('image:current')."</div>";
            echo "<div class='header_preview'>".view('org/custom_header', array('org' => $user))."</div>";
        }
    ?>

    <div class='input'>
            <?php
                if ($user->custom_header)
                {
                    echo __('image:new');
                }
                else
                {
                    echo __('header:chooseimage');
                }
            ?>
        <br />
    <?php

    echo view("input/swfupload_image",
        array(
            'trackDirty' => true,
            'sizes' => User::getHeaderSizes(),
            'thumbnail_size' => 'large',
            'internalname' => 'header',
        ))
    ?>
    <div class='help'>
    <?php echo __('header:constraints') ?>
    </div>
    </div>
</div>


<?php
echo view('input/submit',array(
    'value' => __('savechanges'),
    'trackDirty' => true,
));

?>

</div>


<div class='section_header'><?php echo __("theme"); ?></div>
<div class='section_content padded'>

<?php echo view('input/theme', array(
    'internalname' => 'theme',
    'value' => $user->theme,
    'options' => $user->getAvailableThemes(),
    'previewUrl' => $user->getURL()
)); ?>

<?php
echo view('input/hidden', array('internalname' => 'guid', 'value' => $user->guid));

echo view('input/submit',array(
    'value' => __('savechanges'),
    'trackDirty' => true,
));
?>
</div>
</form>
