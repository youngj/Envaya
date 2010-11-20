<?php

class Notification
{
	const Batch = 1;
	const Comments = 2;
	
	static function all()
	{
		return array(static::Batch, static::Comments);
	}
	
	static function get_options()
	{
		return array(
			static::Batch => __('email:subscribe_reminders'),
			static::Comments => __('email:subscribe_comments'),
		);
	}

}