<?php

	/**
	 * Elgg user display (small)
	 * 
	 * @package ElggProfile
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 * 
	 * @uses $vars['entity'] The user entity
	 */

		$icon = elgg_view(
				"profile/icon", array(
										'entity' => $vars['entity'],
										'size' => 'small',
									  )
			);
			
		$banned = $vars['entity']->isBanned();
	
		// Simple XFN
		$rel = "";
		
		if (!$banned) {
			$info .= "<p><b><a href=\"" . $vars['entity']->getUrl() . "\" rel=\"$rel\">" . escape($vars['entity']->name) . "</a></b></p>";
			//create a view that a status plugin could extend - in the default case, this is the wire
	 		$info .= elgg_view("profile/status", array("entity" => $vars['entity']));

		}
		else
		{
			$info .= "<p><b><strike>";
			if (isadminloggedin())
				$info .= "<a href=\"" . $vars['entity']->getUrl() . "\">";
			$info .= escape($vars['entity']->name);
			if (isadminloggedin())
				$info .= "</a>";
			$info .= "</strike></b></p>";
			
		}
		
		echo elgg_view_listing($icon, $info);
			
?>