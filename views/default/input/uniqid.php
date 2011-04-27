<?php
    /* 
     * A hidden field (default name 'uniqid') that generates a random and hopefully unique value. 
     * This is useful for non-idempotent requests, since it allows the server to check if the user
     * clicked the submit button twice (both requests will have the same uniqid value).
     */
    echo view('input/hidden', array(
        'name' => @$vars['name'] ?: 'uniqid',
        'value' => uniqid("",true)
    ));
