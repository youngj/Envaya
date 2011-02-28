<?php

/*
 * Interface for Google's recaptcha library
 */
class Recaptcha
{
	static function get_html()
	{
		static::load_lib();
		return recaptcha_get_html(Config::get('recaptcha_key'));
	}
	
	static function check_answer()
	{
		static::load_lib();
		return recaptcha_check_answer(Config::get('recaptcha_private'),
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
	}
	
	static function load_lib()
	{
		require_once(Config::get('path')."vendors/recaptchalib.php");
	}
}