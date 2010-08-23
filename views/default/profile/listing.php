<?php

		$icon = view(
				"profile/icon", array(
										'entity' => $vars['entity'],
										'size' => 'small',
									  )
			);
			
		$banned = $vars['entity']->is_banned();
	
		// Simple XFN
		$rel = "";
		
		if (!$banned) {
			$info .= "<p><b><a href=\"" . $vars['entity']->get_url() . "\" rel=\"$rel\">" . escape($vars['entity']->name) . "</a></b></p>";
			//create a view that a status plugin could extend - in the default case, this is the wire
	 		$info .= view("profile/status", array("entity" => $vars['entity']));

		}
		else
		{
			$info .= "<p><b><strike>";
			if (Session::isadminloggedin())
				$info .= "<a href=\"" . $vars['entity']->get_url() . "\">";
			$info .= escape($vars['entity']->name);
			if (Session::isadminloggedin())
				$info .= "</a>";
			$info .= "</strike></b></p>";
			
		}
		
		echo view('search/listing',array('icon' => $icon, 'info' => $info));
			
?>