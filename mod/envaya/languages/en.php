<?php
	/**
	 * Elgg groups plugin language pack
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	$english = array(
			'org:name' => 'Group name',
			'org:username' => 'Group short name (displayed in URLs, alphanumeric characters only)',
			'org:description' => 'Description',
			'org:briefdescription' => 'Brief description',
			'org:interests' => 'Tags',
			'org:website' => 'Website',
			'org:members' => 'Members',
			'org:owner' => "Owner",
			'org:location' => 'Location',
            'org:map' => 'Map',

			'item:group:organization' => "Organizations",

			'org:new' => "Create Organization",
			'org:edit' => "Edit Organization",
			'org:delete' => 'Delete Organization',
			'org:noaccess' => 'You cannot access this organization',
			'org:icon' => 'Organization logo (leave blank to leave unchanged)',
			
			'org:access:private' => 'Closed - Users must be invited',
			'org:access:public' => 'Open - Any user may join',
			'org:cantedit' => 'You can not edit this organization',
			'org:notitle' => 'Organizations must have a title',
			'org:saved' => 'Organization saved',
			
			'org:deletewarning' => "Are you sure you want to delete this organization? There is no undo!",
			'org:deleted' => 'Organization deleted',
			'org:notdeleted' => 'Organization could not be deleted',
			
			'org:notfound' => "Organization not found",
			'org:notfound:details' => "The requested organization either does not exist or you do not have access to it",
			
			
			'org:rejected' => 'We are sorry. This organization was not approved by our administrators.',
			'org:waitforapproval' => "This organization is not yet visible to the public. We will review your profile shortly.",
			'org:waitingapproval' => "This organization is awaiting review and is not yet visible to the public.",
			'org:approve' => "Approve Organization",
			'org:approveconfirm' => "Are you sure you want to approve this organization?",
			'org:approved' => "Organization approved",
			'org:notapproved' => "Organization could not be approved",
			'org:shortnotapproved' => "Not yet approved",
			
			'org:searchnoresults' => "No results found!",
			
	);

	add_translation("en",$english);
?>