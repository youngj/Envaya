<?php
	/**
	 * An HTML form.
     * Automatically includes the security token to prevent CSRF attacks.     
	 */
	
    $body = null;                   // The HTML body of the form 
    $disable_security = false;      // true to disable the CSRF security token
    extract($vars);                
    
    $attrs = Markup::get_attrs($vars, array(
        'enctype' => null,
        'method' => 'POST',
        'action' => null,
        'class' => null,
        'name' => null,
        'style' => null,
        'id' => null,
    ));    
    
    echo "<form ".Markup::render_attrs($attrs).">";    
	if (!$disable_security)
	{
		echo view('input/securitytoken');
	}
    echo $body;
    echo "</form>";
