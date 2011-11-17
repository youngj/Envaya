<?php

class Captcha_Mock extends Captcha
{
    function get_html() 
	{
        return view('captcha/mock');
	}
	
    function show() {}
    
	function check_answer($response)
	{
        return $_POST['captcha_answer'] == $response;
	}	   
}
