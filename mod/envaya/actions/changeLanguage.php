<?php

    action_gatekeeper();
    
    $newLanguage = get_input('newLang');
    
    setcookie("lang", $newLanguage, time() + 60 * 60 * 24 * 365 * 15, '/');
    
    forward($_SERVER['HTTP_REFERER']);
?>