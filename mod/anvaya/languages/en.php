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
	);

	add_translation("en",$english);
?>