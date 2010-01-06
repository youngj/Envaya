<?php
	/**
	 * Browse Organizations
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */
	 global $CONFIG;
	 
	 $context = get_context();
	 $users = get_entities("group","organization",0,10,false);

	 page_draw("Browse all Organizations", elgg_view('extensions/entity_list',array(
  		'entities' => $users
      )));
      
      echo "<a href=\"" . $CONFIG->wwwroot . "pg/org/new/" . "\">". elgg_echo('org:new') ."</a>";
?>