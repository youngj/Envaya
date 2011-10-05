<?php

/*
 * Interface for Securimage captcha library. If Config::get('captcha_enabled') is false, 
 * it will use a simple, fake (insecure) captcha implementation instead
 */
class Captcha
{
	static function get_html()
	{
        if (Config::get('captcha_enabled'))
        {
            static::load_lib();
            Session::start();            
            echo "<img src='/pg/show_captcha?r=".uniqid('',true)."' width='180' height='60' />";            
        }
        else
        {
            return view('captcha/fake');
        }
	}
	
    static function show()
    {
        if (Config::get('captcha_enabled'))
        {
            static::load_lib();
            
            $session = Session::get_instance();            
            $session->start();
            $session->set_dirty(); // Securimage writes to $_SESSION directly
            
            $image = new Securimage();
            $image->use_wordlist = true;     
            $image->image_width = 180;     
            $image->image_height = 60;     
            $image->perturbation  = 0.6;
            $image->num_lines = 4;
            $image->text_color  = new Securimage_Color(rand(0,100),rand(0,100),rand(0,100));
            $image->line_color  = new Securimage_Color(rand(0,200),rand(0,200),rand(0,200));
            $image->text_angle_maximum = 8;     
            $image->show();
        }
    }
    
	static function check_answer($response)
	{
        if (Config::get('captcha_enabled'))
        {
            static::load_lib();
            Session::start();
            
            $image = new Securimage();
            return $image->check($response);
        }
        else
        {
            return $_POST['captcha_answer'] == $response;
        }
	}
	
	static function load_lib()
	{
		require_once(Config::get('root')."/vendors/securimage/securimage.php");
	}
}