<?php

	/**
	 * Elgg file thumbnail
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	// Get engine
		require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
	// Get file GUID
		$file_guid = (int) get_input('file_guid',0);
		
	// Get file thumbnail size
		$size = get_input('size','small');
		if ($size != 'small') {
			$size = 'large';
		}
		
	// Get file entity
		if ($file = get_entity($file_guid)) {
			
			if ($file->getSubtype() == "file") {
				
				$simpletype = $file->simpletype;
				if ($simpletype == "image") {
					
					// Get file thumbnail
						if ($size == "small") {
							$thumbfile = $file->smallthumb;
						} else {
							$thumbfile = $file->largethumb;
						}
						
					// Grab the file
						if ($thumbfile && !empty($thumbfile)) {
							$readfile = new ElggFile();
							$readfile->owner_guid = $file->owner_guid;
							$readfile->setFilename($thumbfile);
							$mime = $file->getMimeType();
							$contents = $readfile->grabFile();
							
							header("Content-type: $mime");
							echo $contents;
							exit;
							
						} 
					
				}
				
			}
			
		}
		
?>