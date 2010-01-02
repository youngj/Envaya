<style type="text/css">
#pages_widget .pagination {
    display:none;
}
</style>
<?php

     /**
	 * Elgg pages widget edit
	 *
	 * @package ElggPages
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */
     
     $num_display = (int) $vars['entity']->pages_num;
     
     $pages = list_entities("object", "page_top", page_owner(), $num_display, false);
	 $pagesurl = $vars['url'] . "pg/pages/owned/" . page_owner_entity()->username;
     $pages .= "<div class=\"pages_widget_singleitem_more\"><a href=\"{$pagesurl}\">" . elgg_echo('pages:more') . "</a></div>";
     
     echo "<div id=\"pages_widget\">" . $pages . "</div>";
     
?>