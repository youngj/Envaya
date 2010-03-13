<?php

    if (isset($vars['selected']) && $vars['selected'] == true) {
        $selected = "class=\"selected\"";
    } else {
        $selected = "";
    }
?>
<a <?php echo $selected; ?> href="<?php echo $vars['href']; ?>"><?php echo $vars['label']; ?></a>