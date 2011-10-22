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
    
    abstract function send_notification($event_name, $notifier);
    abstract function send($args);
}