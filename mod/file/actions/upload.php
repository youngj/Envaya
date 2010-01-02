<?php
	/**
	 * Elgg file browser uploader action
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	global $CONFIG;
	
	gatekeeper();
	
	// Get variables
	$title = get_input("title");
	$desc = get_input("description");
	$tags = get_input("tags");
	$access_id = (int) get_input("access_id");
	$container_guid = (int) get_input('container_guid', 0);
	if (!$container_guid)
		$container_guid == $_SESSION['user']->getGUID();
	
	// Extract file from, save to default filestore (for now)
	$prefix = "file/";
	$file = new FilePluginFile();
	$filestorename = strtolower(time().$_FILES['upload']['name']);
	$file->setFilename($prefix.$filestorename);
	$file->setMimeType($_FILES['upload']['type']);
	
	$file->originalfilename = $_FILES['upload']['name'];
	
	$file->subtype="file";
	
	$file->access_id = $access_id;
	
	$file->open("write");
	$file->write(get_uploaded_file('upload'));
	$file->close();
	
	$file->title = $title;
	$file->description = $desc;
	if ($container_guid)
		$file->container_guid = $container_guid;
	
	// Save tags
	$tags = explode(",", $tags);
	$file->tags = $tags;
	
	$file->simpletype = get_general_file_type($_FILES['upload']['type']);
	
	$result = $file->save();

	
	if ($result)
	{	
		
		// Generate thumbnail (if image)
		if (substr_count($file->getMimeType(),'image/'))
		{
			$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),60,60, true);
			$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),153,153, true);
			$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),600,600, false);
			if ($thumbnail) {
				$thumb = new ElggFile();
				$thumb->setMimeType($_FILES['upload']['type']);
				
				$thumb->setFilename($prefix."thumb".$filestorename);
				$thumb->open("write");
				$thumb->write($thumbnail);
				$thumb->close();
				
				$file->thumbnail = $prefix."thumb".$filestorename;
				
				$thumb->setFilename($prefix."smallthumb".$filestorename);
				$thumb->open("write");
				$thumb->write($thumbsmall);
				$thumb->close();
				$file->smallthumb = $prefix."smallthumb".$filestorename;
				
				$thumb->setFilename($prefix."largethumb".$filestorename);
				$thumb->open("write");
				$thumb->write($thumblarge);
				$thumb->close();
				$file->largethumb = $prefix."largethumb".$filestorename;
					
			}
		}
	}
		
	if ($result){
		system_message(elgg_echo("file:saved"));
		add_to_river('river/object/file/create','create',$_SESSION['user']->guid,$file->guid);
    }else
		register_error(elgg_echo("file:uploadfailed"));
		
	$container_user = get_entity($container_guid);
	
	forward($CONFIG->wwwroot . "pg/file/" . $container_user->username);

?>