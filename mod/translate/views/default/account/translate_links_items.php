<?php

    $user = $vars['user'];       

    if (!($user instanceof Organization)) // hmm...
    {
        echo view('account/link_item', array('href' => '/tr', 
            'style' => 'background:url(/_media/images/translate/world.gif) no-repeat 4px 7px;', 
            'text' => __('itrans:translations')))
            ."<div class='icon_separator'></div>";        
    }