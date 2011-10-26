<?php
/*
 * Superclass of anything that someone can subscribe to receive notifications from.
 *
 * Each subscription is associated with an entity (container) 
 * that describes the scope of the subscription
 * (e.g. notifications for a particular user, widget, discussion, etc.)
 *
 * Each notification on a subscription is associated with an arbitrary event name 
 * that represents what happened to trigger the notification (e.g. message added).
 *
 * Concrete subclasses should override send_notification to construct the 
 * message and call send().
 *
 * The subscriber does not necessarily need to be associated with a User.
 *
 * See EmailSubscription and SMSSubscription, 
 * and subclasses in emailsubscription/ and smssubscription/.
 */
abstract class Subscription extends Entity
{
    static function query_for_entity($entity)
    {
        return static::query()->where('container_guid = ?', $entity->guid);
    }
    
    /*
     * Notifies subscribers for this subscription type 
     * on all entities containing $notifier (not including $notifier itself)
     */
    static function send_notifications($event_name, $notifier)
    {                        
        foreach (static::get_subscriptions($notifier) as $subscription)
        {
            $subscription->send_notification($event_name, $notifier);
        }
    }
    
    /*
     * Gets a list of all subscriptions for this subscription type
     * on all entities containing $notifier (not including $notifier itself)
     */
    static function get_subscriptions($notifier)
    {
        $cur = $notifier->get_container_entity();                   
        
        $subscription_lists = array();
        
        while ($cur != null)
        {
            $subscription_lists[] = static::query_for_entity($cur)->filter();
        
            $next = $cur->get_container_entity();
            if ($next == $cur)
            {
                break;
            }
            $cur = $next;
        }
        
        return static::merge($subscription_lists);
    }
    
    /*
     * Merges lists of subscriptions into a single list, omitting duplicate keys
     * (e.g. if the same email address would receive the notification in two different ways)
     */
    static function merge($subscription_lists)
    {
        $res = array();
        $keys = array();
        
        foreach ($subscription_lists as $subscriptions)
        {
            foreach ($subscriptions as $subscription)
            {
                $key = $subscription->get_key();
                
                if (!isset($keys[$key]))
                {
                    $keys[$key] = true;
                    $res[] = $subscription;
                }
            }
        }
        
        return $res;
    }   
    
    abstract function get_key();
    
    abstract function get_recipient_description();
    
    abstract function send_notification($event_name, $notifier);
    abstract function send($args);
}