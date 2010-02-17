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
     
    $users = get_entities("group","organization",0,10,false);

    $body = elgg_view('extensions/entity_list',array(
        'entities' => $users
    ));

    page_draw(elgg_echo("org:browse"), elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("org:browse")), $body));
     
    
?>