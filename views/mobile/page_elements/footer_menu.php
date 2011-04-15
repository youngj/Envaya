<div style='padding:0px 10px'>

<?php
    $site_url = @$vars['site_url'];
    if ($site_url)
    {
        ?>
        <form style='display:inline' method='GET' action='<?php echo escape($site_url); ?>'><input class='button_alt' type='submit' value='<?php echo __('widget:home'); ?>' /></form>
        <?php
    }
?>
<form style='display:inline' method='GET' action='/'><input class='button_alt' type='submit' value='<?php echo __('menu'); ?>' /></form>

</div>