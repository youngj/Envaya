<?php
    $user = $vars['user'];
    if ($user->equals(Session::get_logged_in_user()) && Permission_UseAdminTools::has_any())
    {
        echo implode("<div class='icon_separator'></div>",
            array(
                view('account/link_item', array(
                    'href' => '/admin/contact', 
                    'text' => __('contact:user_list'),
                    'class' => 'icon_admin'
                )),
                view('account/link_item', array(
                    'href' => '/admin/contact/email', 
                    'text' => sprintf(__('contact:template_list'), __('contact:email')),
                    'class' => 'icon_admin'
                )),
                view('account/link_item', array(
                    'href' => '/admin/contact/sms', 
                    'text' => sprintf(__('contact:template_list'), __('contact:sms')),
                    'class' => 'icon_admin'
                )),
            )
        );
        echo "<div class='icon_separator'></div>";
    }
        