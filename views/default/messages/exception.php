<div class='section_content padded'>
<?php

	/**
	 * Displays a single exception
	 * @uses $vars['object'] An exception
	 */

    echo "<b>".view('output/longtext', array('value' => $vars['object']->getMessage()))."</b>";
    
	if (Config::get('debug'))
	{
		echo "<hr />";
		echo "<p class='messages-exception-detail'><pre>";
        echo escape(print_r($vars['object'], true));
        echo "</pre></p>";
	}		
?>
</div>