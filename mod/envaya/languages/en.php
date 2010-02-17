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
            'welcome' => "Welcome to Envaya",
            'welcome_user' => "Welcome, %s!",
            'user_orgs' => "Your organizations",
            'actions' => "Actions",
            'org:about' => 'About',
            'org:view' => 'View profile',
            'org:edit' => 'Edit profile',
            'org:updates' => 'Updates',
            'org:logo' => 'Logo',
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
            'org:blog' => 'News',
            'org:language' => 'Language',
            'org:browse' => "Browse all Organizations",
            'translation_by' => 'Translation by',

			'item:group:organization' => "Organizations",

			'org:new' => "Create Organization",
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

			'org:mobilesettings' => "Mobile Settings",
			'org:postemail' => 'Posting Email',
            'org:changeemail' => 'Change',

        /**
         * Menu items and titles
         */

            'blog' => "Blog",
            'blogs' => "Blogs",
            'blog:user' => "%s's blog",
            'blog:user:friends' => "%s's friends' blog",
            'blog:your' => "Your blog",
            'blog:posttitle' => "%s's blog: %s",
            'blog:friends' => "Friends' blogs",
            'blog:yourfriends' => "Your friends' latest blogs",
            'blog:everyone' => "All site blogs",
            'blog:newpost' => "New blog post",
            'blog:via' => "via blog",
            'blog:read' => "Read blog",

            'blog:addpost' => "Add update",
            'blog:editpost' => "Edit",

            'blog:text' => "Blog text",

            'blog:strapline' => "%s",

            'item:object:blog' => 'Blog posts',

            'blog:never' => 'never',
            'blog:preview' => 'Preview',

            'blog:draft:save' => 'Save draft',
            'blog:draft:saved' => 'Draft last saved',
            'blog:comments:allow' => 'Allow comments',

            'blog:preview:description' => 'This is an unsaved preview of your blog post.',
            'blog:preview:description:link' => 'To continue editing or save your post, click here.',

            'blog:enableblog' => 'Enable group blog',

            'blog:group' => 'Group blog',

         /**
         * Blog river
         **/

            //generic terms to use
            'blog:river:created' => "%s wrote",
            'blog:river:updated' => "%s updated",
            'blog:river:posted' => "%s posted",

            //these get inserted into the river links to take the user to the entity
            'blog:river:create' => "a new blog post titled",
            'blog:river:update' => "a blog post titled",
            'blog:river:annotate' => "a comment on this blog post",


        /**
         * Status messages
         */

            'blog:posted' => "Your blog post was successfully posted.",
            'blog:deleted' => "Your blog post was successfully deleted.",

        /**
         * Error messages
         */

            'blog:error' => 'Something went wrong. Please try again.',
            'blog:save:failure' => "Your blog post could not be saved. Please try again.",
            'blog:blank' => "Sorry; you need to fill in both the title and body before you can make a post.",
            'blog:notfound' => "Sorry; we could not find the specified blog post.",
            'blog:notdeleted' => "Sorry; we could not delete this blog post.",

			'org:verify' => "Verify Organization",
			'org:verifyconfirm' => "Are you sure you want to verify this organization is legitimate?",
			'org:verified' => "Organization verified",
			'org:notverified' => "Organization could not be verified",


	);

	add_translation("en",$english);
?>