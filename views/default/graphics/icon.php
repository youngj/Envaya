<?php
    /**
     * Generic icon view.
     */

    $entity = $vars['entity'];

    // Get any align and js
    if (!empty($vars['align'])) {
        $align = " align=\"{$vars['align']}\" ";
    } else {
        $align = "";
    }


?>
<div class="icon">
<?php if (@$vars['link']) { ?><a href="<?php echo @$vars['link'] ?>"><?php } ?>
<img src="<?php echo $entity->get_icon($vars['size']); ?>" border="0" <?php echo $align; ?> <?php echo @$vars['js']; ?> />
<?php if (@$vars['link']) { ?></a><?php } ?>
</div>