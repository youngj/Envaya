<?php
    
    $newLanguage = get_input('newLang');
    
    change_viewer_language($newLanguage);
    
    forward_to_referrer();    
?>