<?php
    
    $newLanguage = get_input('newLang');
    
    setcookie("lang", $newLanguage, time() + 60 * 60 * 24 * 365 * 15, '/');
    
    forward_to_referrer();
?>