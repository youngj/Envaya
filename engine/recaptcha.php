<?php

/*
 * Interface for Google's recaptcha library. If Config::get('recaptcha_enabled') is false, 
 * it will use a simple, fake (insecure) captcha implementation instead
 */
class Recaptcha
{
	static function get_html()
	{
        if (Config::get('recaptcha_enabled'))
        {
            static::load_lib();
            return recaptcha_get_html(Config::get('recaptcha_key'));
        }
        else
        {
            return view('test/fake_captcha');
        }
	}
	
	static function check_answer()
	{
        if (Config::get('recaptcha_enabled'))
        {
            static::load_lib();
            return recaptcha_check_answer(Config::get('recaptcha_private'),
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
        }
        else
        {
            $res = new stdClass;
            $res->is_valid = $_POST['recaptcha_challenge_field'] == $_POST['recaptcha_response_field'];
            return $res;
        }
	}
	
	static function load_lib()
	{
		require_once(Config::get('path')."vendors/recaptchalib.php");
	}
}