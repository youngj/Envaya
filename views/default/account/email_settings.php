<div class='section_content padded'>
<?php
    $email = $vars['email'];
    $subscriptions = $vars['subscriptions'];
    $show_more = $vars['show_more'];
    $code = EmailSubscription::get_email_fingerprint($email);

?>
<form action='/pg/email_settings' method='POST'>

<div class='input'>

<?php
    echo view('input/securitytoken');

    echo view('input/hidden', array(
        'name' => 'email',
        'value' => $email
    ));

    echo view('input/hidden', array(
        'name' => 'code',
        'value' => $code
    ));

?>

    <div class='input'>
    <label><?php echo sprintf(__('email:subscriptions'), "<em>".escape($email)."</em>"); ?></label><br />
    <?php
            if ($show_more)
            {
                $url = EmailSubscription::get_all_settings_url($email);
                echo "<div style='float:right;font-size:12px'><a href='{$url}'>".__('email:all_settings')."</a></div>";
            }    
    
            echo view('pagination', array(
                'offset' => $vars['offset'],
                'count' => $vars['count'],
                'limit' => $vars['limit'],
            ));
    
            $options = array();
            $value = array();
            
            foreach ($subscriptions as $subscription)
            {
                $options[$subscription->guid] = $subscription->get_description();
                
                if ($subscription->is_enabled())
                {
                    $value[] = $subscription->guid;
                }
                
                echo view("input/hidden", array(
                    'name' => 'subscriptions[]', 
                    'value' => $subscription->guid, 
                ));
            }
            
            echo view("input/checkboxes", array(
                'name' => 'enabled_subscriptions', 
                'value' => $value, 
                'options' => $options
            ));                        

     ?>
     </div>

     <div class='input'>
     <label><?php echo __('language'); ?></label><br />
     <?php
            echo view("input/language", array(
                'name' => 'language', 
                'value' => $subscriptions[0]->language, 
            ));                        

     ?>
     </div>
     
</div>

<?php

echo view('input/submit',array(
    'value' => __('savechanges')
));

?>

</form>
</div>