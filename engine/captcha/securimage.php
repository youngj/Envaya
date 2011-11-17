<?php

class Captcha_Securimage extends Captcha
{
	function load_lib()
	{
		require_once(Engine::$root."/vendors/securimage/securimage.php");
	}

    function get_html() 
    {
        $this->load_lib();
        Session::start();
        echo "<img src='/pg/show_captcha?r=".uniqid('',true)."' width='180' height='60' />";
    }
    
    function show()
    {
        $this->load_lib();
            
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
    
    function check_answer($response)
    {
        $this->load_lib();
        Session::start();
            
        $image = new Securimage();
        return $image->check($response);    
    }
}