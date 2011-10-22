<?php
    $user = @$vars['user'];
    $subscription = $vars['subscription'];
    
    echo "<br /><br /><div style=\"font-size:10px;color:#996600;line-height:100%;font-family:verdana;\">";

    if ($subscription)
    {
        $lang = $subscription->language;

        echo strtr(__('email:about',$lang), array(
            '{email}' => escape($subscription->email), 
            '{name}' => escape($subscription->get_name())
        ));
        echo "<br /><br />";

        echo sprintf(
            __('email:unsubscribe',$lang),
            "<a target='_blank' href='{$subscription->get_settings_url()}'>".__('here',$lang)."</a>"
        );
        echo "<br /><br />";
    }
    
    echo Config::get('contact:email_footer_html'); 
    echo "<br /></div>";
