<?php
    $footer = PageContext::get_submenu('footer');    
    echo implode(' &middot; ', $footer->render_items());
