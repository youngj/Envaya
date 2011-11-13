<?php
    $footer = PageContext::get_submenu('footer');    
    $content = implode(' &middot; ', $footer->get_items());
	
	if ($content)
	{
		echo "<div class='footerLinks'>$content</div>";
	}

