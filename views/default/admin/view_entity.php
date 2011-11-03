<div class='section_content padded'>
<?php
    $entity = $vars['entity'];
    
    $cls = get_class($entity);
    $view_name = $cls::$admin_view;
    if ($view_name)
    {
        echo view($view_name, array('entity' => $entity));
        echo "<br />";
    }        
    
    $render_groups = function($groups)
    {
        return view('admin/item_groups', array('item_groups' => $groups));
    };
    
    $permissions = Permission::query_for_entity($entity)->filter();    
    
    if ($permissions)
    {
        echo "<h4>Permissions (".sizeof($permissions).")</h4>";
        
        $user_groups = array();
        $subtype_groups = array();
        
        foreach ($permissions as $permission)
        {
            $owner = $permission->get_owner_entity();
            $title_html = escape($permission->get_title());
            
            if ($owner)
            {
                $owner_html = "<a href='{$owner->get_admin_url()}'>".escape($owner->username)."</a>";
            }
            else
            {
                $owner_html = $permission->owner_guid;
            }            
            $user_groups[$owner_html][] = $title_html;
            $subtype_groups[$title_html][] = $owner_html;
        }        
        
        echo $render_groups($user_groups);
        echo "<br />";
        echo $render_groups($subtype_groups);
        echo "<br />";
    }        
    
    $email_subscriptions = EmailSubscription::query_for_entity($entity)->filter();    
    if ($email_subscriptions)
    {       
        $type_groups = array();
        $email_groups = array();
        
        foreach ($email_subscriptions as $subscription)
        {
            $desc = escape($subscription->get_description());
            $email_link = "<a href='{$subscription->get_settings_url()}'>".escape($subscription->email)."</a>";            
            $desc_link = "<a href='{$subscription->get_settings_url()}'>".escape($subscription->get_description())."</a>";
            
            $type_groups[$desc][] = $email_link;
            $email_groups[escape($subscription->email)][] = $desc_link;
        }
        
        echo "<h4>Email Subscriptions (".sizeof($email_subscriptions).")</h4>";
        echo $render_groups($email_groups);        
        echo "<br />";        
        echo $render_groups($type_groups);        
        echo "<br />";
        
    }    
    
    $sms_subscriptions = SMSSubscription::query_for_entity($entity)->filter();    
    if ($sms_subscriptions)
    {
        $type_groups = array();
        $phone_groups = array();
        foreach ($sms_subscriptions as $subscription)
        {
            $phone_link = "<a href='{$subscription->get_admin_url()}'>".escape($subscription->phone_number)."</a>";
            $desc_link = "<a href='{$subscription->get_admin_url()}'>".escape($subscription->get_description())."</a>";
        
            $type_groups[escape($subscription->get_description())][] = $phone_link;
            $phone_groups[escape($subscription->phone_number)][] = $desc_link;
        }
        
        echo "<h4>SMS Subscriptions (".sizeof($sms_subscriptions).")</h4>";
        echo $render_groups($phone_groups);        
        echo "<br />";
        echo $render_groups($type_groups);
        echo "<br />";
    }        
    
    $url = $entity->get_url();
    
    if ($url)
    {   
        $esc_url = escape($url);
        echo "URL: <a href='$esc_url'>".abs_url($esc_url)."</a><br />";
    }
        
    echo "Created: ".friendly_time($entity->time_created)."<br />";
    
    if ($entity->time_updated > $entity->time_created)
    {
        echo "Updated: ".friendly_time($entity->time_updated)."<br />";
    }
    
    $owner = $entity->get_owner_entity();
    
    if ($owner)
    {
        echo "Owner: <a href='{$owner->get_admin_url()}'>".escape($owner->get_title())."</a><br />";
    }
    echo "Type: ".get_class($entity)."<br />";
    
    echo "<div class='admin_links'>";
    if ($entity->is_enabled())
    {
        echo view('input/post_link', array(
            'text' => __('entity:disable'),
            'confirm' => __('areyousure'),        
            'href' => "{$entity->get_admin_url()}/disable"
        ));        
    }
    else
    {
        echo view('input/post_link', array(
            'text' => __('entity:enable'),
            'confirm' => __('areyousure'),        
            'href' => "{$entity->get_admin_url()}/enable"
        ));            
    }
    echo "</div>";
?>
</div>