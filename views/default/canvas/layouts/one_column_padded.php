<?php

	/**
	 * Elgg one-column layout
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */
    
    echo elgg_view_layout('one_column', $vars['area1'], "<div class='section_content'>".$vars['area2']."</div>", $vars['area3']);    
?>
