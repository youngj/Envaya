<?php
    /**
     * CSRF security token view for use with secure forms.
     */

    $ts = time();
    $token = generate_security_token($ts);

    echo view('input/hidden', array('name' => '__token', 'value' => $token));
    echo view('input/hidden', array('name' => '__ts', 'value' => $ts));
?>
