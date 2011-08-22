<?php
    $viewTypes = array('default', 'mobile');
    $curViewType = Views::get_request_type();
    $links = array();
    
    foreach ($viewTypes as $viewType)
    {
        $text = __("viewtype:$viewType");
    
        if ($viewType == $curViewType)
        {
            $links[] = "<strong>".$text."</strong>";
        }
        else
        {
            $url = url_with_param($vars['original_url'], 'view', $viewType);
            $links[] = "<a rel='nofollow' href='".escape($url)."'>$text</a>";
        }           
    }
    echo implode(' &middot; ', $links);
    
?>
