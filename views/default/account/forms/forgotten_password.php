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
    $form_body .= "<p><label>". __('username') . " " . view('input/text', array('internalname' => 'username')) . "</label></p>";
    $form_body .= view('input/captcha');
    $form_body .= "<p>" . view('input/submit', array('value' => __('request'))) . "</p>";

?>
    <?php echo view('input/form', array('action' => "{$vars['url']}pg/request_new_password", 'body' => $form_body)); ?>
