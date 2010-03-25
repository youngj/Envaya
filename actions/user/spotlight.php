<?php

		gatekeeper();
		
		$closed = get_input('closed','true');
		if ($closed != 'true') {
			$closed = false;
		} else {
			$closed = true;
		}
		
		get_loggedin_user()->spotlightclosed = $closed;
		exit;

?>