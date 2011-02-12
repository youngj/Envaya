<?php

    /**
     * @uses $vars['entity'] The user entity. If none specified, the current user is assumed.
     * @uses $vars['size'] The size - small, medium or large. If none specified, medium is assumed.
     */

    // Get entity
        if ($vars['entity'] instanceof User) {

        $name = htmlentities($vars['entity']->name, ENT_QUOTES, 'UTF-8');
        $username = $vars['entity']->username;

        if ($icontime = $vars['entity']->icontime) {
            $icontime = "{$icontime}";
        } else {
            $icontime = "default";
        }

    // Get size
        if (!in_array($vars['size'],array('small','medium','large','tiny','master','topbar')))
            $vars['size'] = "medium";

    // Get any align and js
        if (!empty($vars['align'])) {
            $align = " align=\"{$vars['align']}\" ";
        } else {
            $align = "";
        }

    ?><img src="<?php echo $vars['entity']->get_icon($vars['size']); ?>" border="0" <?php echo $align; ?> title="<?php echo htmlentities($vars['entity']->name, ENT_QUOTES, 'UTF-8'); ?>" <?php echo @$vars['js']; ?> /><?php
 }

?>