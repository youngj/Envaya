<?php
    $user = @$vars['user'];
    
    echo "<br /><br /><div style=\"font-size:10px;color:#996600;line-height:100%;font-family:verdana;\">";

    if ($user)
    {
        $lang = $user->language;

        echo strtr(__('email:about',$lang), array(
            '{email}' => escape($user->email), 
            '{name}' => escape($user->name)
        ));
        echo "<br /><br />";

        echo sprintf(
            __('email:unsubscribe',$lang),
            "<a target='_blank' href='{$user->get_email_settings_url()}'>".__('here',$lang)."</a>"
        );
        echo "<br /><br />";
    }
    
    echo Config::get('contact:email_footer_html'); 
    echo "<br /></div>";
