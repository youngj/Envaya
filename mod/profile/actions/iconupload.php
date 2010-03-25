<?php

	/**
	 * Elgg profile plugin upload new user icon action
	 *
	 * @package ElggProfile
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	gatekeeper();
	action_gatekeeper();

	$user = page_owner_entity();
	if (!$user)
		$user = get_loggedin_user();

	// If we were given a correct icon
		if (isloggedin() && $user && $user->canEdit() && has_uploaded_file('profileicon'))
        {
            $user->setIcon(get_uploaded_filename('profileicon'));


            system_message(elgg_echo("profile:icon:uploaded"));

            trigger_elgg_event('profileiconupdate',$user->type,$user);
        } 
        else 
        {
            system_message(elgg_echo("profile:icon:notfound"));
        }

	    //forward the user back to the upload page to crop

	    $url = "pg/profile/{$user->username}/editicon/";

		if (isloggedin()) forward($url);

?>