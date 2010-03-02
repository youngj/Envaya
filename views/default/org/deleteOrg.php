<div class="contentWrapper">
<div id="delete_group_option">
    <form action="<?php echo $vars['url'] . "action/deleteOrg"; ?>">
        <?php echo elgg_view('input/securitytoken'); ?>
        <?php
            if ($vars['entity'])
            {
                $warning = elgg_echo("org:deletewarning");
            ?>
            <input type="hidden" name="user_guid" value="<?php echo page_owner_entity()->guid; ?>" />
            <input type="hidden" name="org_guid" value="<?php echo $vars['entity']->getGUID(); ?>" />
            <input type="submit" name="delete" value="<?php echo elgg_echo('org:delete'); ?>" onclick="javascript:return confirm('<?php echo $warning; ?>')"/><?php
            }
        ?>
    </form>
</div>



