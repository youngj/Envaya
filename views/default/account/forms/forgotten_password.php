<?php
    /**
     * Elgg forgotten password.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    $form_body = "<p>" . __('user:password:text') . "</p>";
    $form_body .= "<p><label>". __('username') . " " . elgg_view('input/text', array('internalname' => 'username')) . "</label></p>";
    $form_body .= elgg_view('input/captcha');
    $form_body .= "<p>" . elgg_view('input/submit', array('value' => __('request'))) . "</p>";

?>
    <?php echo elgg_view('input/form', array('action' => "{$vars['url']}pg/request_new_password", 'body' => $form_body)); ?>
