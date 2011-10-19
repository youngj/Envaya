<?php

/*
 * Defines constants for an organization's notification settings 
 * (e.g. which emails or SMS messages they subscribe to)
 */
class Notification
{
	const Batch = 1;        // emails sent by administrators to all users
	const Comments = 2;     // emails sent when someone leaves a comment on one of their news updates
    const Network = 4;      // emails sent when someone adds this organization to their network page
    const Discussion = 8;   // emails sent when someone adds a message or topic on their discussions page
	
	static function all()
	{
		return array(static::Batch, static::Comments, static::Network, static::Discussion);
	}
	
	static function get_options()
	{
		return array(
			static::Batch => __('user:subscribe_reminders'),
			static::Comments => __('user:subscribe_comments'),
            static::Discussion => __('user:subscribe_discussion'),
            static::Network => __('user:subscribe_network'),
		);
	}
}