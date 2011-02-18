<?php

	/**
	 * Lists all system messages
	 * @uses $vars['object'] The array of message registers
	 */
    if (!empty($vars['object']) && is_array($vars['object']) && sizeof($vars['object']) > 0) {
        
        foreach($vars['object'] as $register => $list ) {
            echo view("messages/{$register}/list", array('object' => $list));
        }
        
    }
		
?>