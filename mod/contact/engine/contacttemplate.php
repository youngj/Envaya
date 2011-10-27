<?php

/*
 * Common functionality for sending batch messages to subscribers via any contact method (email or SMS).
 *
 * The message can contain placeholder strings with properties of a User or Organization,
 * (e.g. {{username}}), which will be replaced with the appropriate values for each user.
 */
abstract class ContactTemplate extends Entity
{
    const Sent = 'sent';

    static $allowed_placeholders = array(
        'username',
        'name',
        'email',
    );    
        
    static $outgoing_message_class;
    static $subscription_class;
    static $count_filters_url;

    static $table_attributes = array(
        'num_sent' => 0,
        'time_last_sent' => 0,
        'filters_json' => '',
    );
    
    static $mixin_classes = array(
        'Mixin_Content'
    );        
    
    protected $filters;    
    
    function update()
    {
        $this->num_sent = $this->query_outgoing_messages()->count();        
        $outgoing_message = $this->query_outgoing_messages()->order_by('id desc')->get();
        $this->time_last_sent = $outgoing_message ? $outgoing_message->time_created : 0;
        $this->save();
    }    
    
    static function query_all_subscriptions()
    {
        $subscription_class = static::$subscription_class;
        return $subscription_class::query();
    }            
            
    static function query_subscriptions($filters)
    {
        $user_query = User::query()->columns('guid')->apply_filters($filters);
    
        return static::query_all_subscriptions()
            ->where("container_guid in ({$user_query->get_sql()})", $user_query->get_args());
    }    
    
    function query_outgoing_messages()
    {
        $outgoing_message_class = static::$outgoing_message_class;
        return $outgoing_message_class::query()->where('notifier_guid = ?', $this->guid);
    }    
        
    function query_potential_recipients()
    {
        $outgoing_message_class = static::$outgoing_message_class;
        $outgoing_message_table = $outgoing_message_class::$table_name;
        $subscription_class = static::$subscription_class;
        $subscription_table = $subscription_class::$table_name;
    
        return $this->query_filtered_subscriptions()
            ->where("not exists (select * from $outgoing_message_table where notifier_guid = ? and subscription_guid = $subscription_table.guid)", 
                $this->guid);        
    }
        
    function query_filtered_subscriptions()
    {
        return static::query_subscriptions($this->get_filters());
    }                           
    
    function query_outgoing_messages_for_subscription($subscription)
    {
        return $this->query_outgoing_messages()
            ->where('subscription_guid = ?', $subscription->guid);
    }    
    
    function get_filters()
    {
        if (!isset($this->filters))
        {
            $this->filters = Query_Filter::json_decode_filters($this->filters_json);
        }
        return $this->filters;
    }
    
    function set_filters($filters)
    {
        $this->filters = $filters;
        $this->filters_json = Query_Filter::json_encode_filters($filters);
    }    
    
    function render($content, $subscription)
    {
        $args = array();
        
        $user = $subscription ? $subscription->get_container_entity() : null;
        if ($user)
        {
            foreach (static::$allowed_placeholders as $prop)
            {
                $value = $user->$prop;
                $args["{{".$prop."}}"] = $value;
                $args["%7B%7B".$prop."%7D%7D"] = $value; // {{ }} may be urlencoded 
            }
        }
   
        return strtr($content, $args);
    }    
    
    function can_send_to($subscription)
    {    
        $subscription_class = static::$subscription_class;
    
        return $subscription->is_enabled() && $subscription->subtype_id == $subscription_class::get_subtype_id()
            && $this->query_outgoing_messages_for_subscription($subscription)->is_empty();
    }    
    
    function render_content($subscription)
    {
        return $this->render($this->content, $subscription);
    }    
    
    abstract function get_description();
}