<?php

class Recaptcha
{
	static function get_html()
	{
		static::load_lib();
		global $CONFIG;
		return recaptcha_get_html($CONFIG->recaptcha_key);
	}
	
	static function check_answer()
	{
		static::load_lib();
		global $CONFIG;
		return recaptcha_check_answer($CONFIG->recaptcha_private,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
	}
	
	static function load_lib()
	{
		global $CONFIG;
		require_once("{$CONFIG->path}vendors/recaptchalib.php");
	}
}