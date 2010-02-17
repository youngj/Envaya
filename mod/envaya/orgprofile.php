<?php
	/**
	 * Full org profile
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	$org_guid = get_input('org_guid');
	set_context('org');

	global $autofeed;
	$autofeed = false;

	$org = get_entity($org_guid);
	if ($org) {
		set_page_owner($org_guid);                
        
		$title = $org->name;	

        $viewOrg = false;
        
        if($org->approval > 0)
        {
            //organization approved
            $viewOrg = true;
        }
	    else if ($org->approval < 0)
	    {
	        //organization rejected
	        $msg = elgg_echo('org:rejected');
        }
        else
        {
            //organization waiting for approval
            if(isadminloggedin())
            {
                $viewOrg = true;
            }
            else if($org->getOwnerEntity() == get_loggedin_user())
            {
                $viewOrg = true;
                $msg = elgg_echo('org:waitforapproval');
            }
            else
            {
                $msg = elgg_echo('org:waitingapproval');
            }
        }

        if ($org->canEdit())
        {
            add_submenu_item(elgg_echo("edit"), $org->getUrl() . "edit");
        }                    
                
        $area2 = elgg_view('org/org', array('entity' => $org, 'user' => $_SESSION['user'], 'full' => $viewOrg, 'msg' => $msg));        
        $area1 = elgg_view('page_elements/title', array('title' => $title, 'subtitle' => $org->location));
        $body = elgg_view_layout('one_column', $area1, $area2);
        
	} else {
		$title = elgg_echo('org:notfound');

		$area2 = elgg_view_title($title);
		$area2 .= elgg_view('org/contentwrapper',array('body' => elgg_echo('org:notfound:details')));

        $body = elgg_view_layout('profile', $area2);
	}

	// Finally draw the page
	page_draw($title, $body);
?>