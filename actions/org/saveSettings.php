<?php    
	global $CONFIG;
	
    gatekeeper();
	action_gatekeeper();
	
    $user_id = get_input('guid');    
    $org = get_entity($user_id);

    if ($org && $org instanceof Organization && $org->canEdit())
    {
        if (get_input('deleteicon'))
        {
            $org->custom_icon = false;
            $org->save();
            
            system_message(elgg_echo("org:icon:reset"));
        }
        if (has_uploaded_file('icon'))
        {
            if (!is_image_upload('icon'))
            {                
                register_error(elgg_echo('upload:invalid_image'));
            }
            else
            {   
                $prefix = "icon";

                $file = new ElggFile();
                $file->owner_guid = $org->guid;
                $file->setFilename($prefix . ".jpg");
                $file->open("write");
                $file->write(get_uploaded_file('icon'));
                $file->close();
                $fn = $file->getFilenameOnFilestore();

                $thumbtiny = get_resized_image_from_existing_file($fn,25,25, true);
                $thumbsmall = get_resized_image_from_existing_file($fn,40,40, true);
                $thumbmedium = get_resized_image_from_existing_file($fn,100,100, true);
                $thumblarge = get_resized_image_from_existing_file($fn,200,200, false);

                if ($thumbtiny) 
                {
                    $thumb = new ElggFile();
                    $thumb->owner_guid = $org->guid;
                    $thumb->setMimeType('image/jpeg');

                    $thumb->setFilename($prefix."tiny.jpg");
                    $thumb->open("write");
                    $thumb->write($thumbtiny);
                    $thumb->close();

                    $thumb->setFilename($prefix."small.jpg");
                    $thumb->open("write");
                    $thumb->write($thumbsmall);
                    $thumb->close();

                    $thumb->setFilename($prefix."medium.jpg");
                    $thumb->open("write");
                    $thumb->write($thumbmedium);
                    $thumb->close();

                    $thumb->setFilename($prefix."large.jpg");
                    $thumb->open("write");
                    $thumb->write($thumblarge);
                    $thumb->close();
                    
                    $org->custom_icon = true;
                    $org->save();

                    system_message(elgg_echo("org:icon:saved"));
                }
                else
                {
                    register_error(elgg_echo('upload:invalid_image'));
                }
            }    
        }
    }    
	
?>