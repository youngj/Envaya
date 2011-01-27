<div style='padding:0px 10px'>

<?php
    $site_org = PageContext::get_site_org();
    if ($site_org)
    {
        ?>
        <form style='display:inline' method='GET' action='<?php echo $site_org->get_url(); ?>'><input class='button_alt' type='submit' value='<?php echo __('widget:home'); ?>' /></form>
        <?php
    }
?>
<form style='display:inline' method='GET' action='/'><input class='button_alt' type='submit' value='<?php echo __('menu'); ?>' /></form>

</div>