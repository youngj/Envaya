<?php
    $widget = $vars['widget'];
    
    $user = $widget->get_root_container_entity();
    $user_phone = $user->get_primary_phone_number();

    if ($user_phone)
    {
        ob_start();
        
        echo "<p>";
        echo "Now you can publish your organization's news to Envaya via SMS!";
        echo "</p>";
        
        $dialed_phone_number = "+".SMS_Service_News::get_phone_number($user_phone);
        
        echo "<p style='text-align:center;font-size:16px'>";
        echo "Text <strong>P <span style='color:#999'>(your message here)</span></strong><br /> to <strong>"
            . $dialed_phone_number . "</strong>";
        echo "</p>";
        
        echo "<p>";
        echo "To learn how to use SMS commands, visit the <a href='http://envaya.org/envaya/widget/using-envaya-via-text-messages-sms,62524'>Help page</a>.";
        echo "</p>";        
        
        $content = ob_get_clean();    
        
        echo view("section", array(
            'header' => "Publishing News Updates via SMS",
            'content' => $content
        ));    
    }
