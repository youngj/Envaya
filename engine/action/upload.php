<?php

class Action_Upload extends Action
{
    protected function upload_file_in_mode($file_input, $mode)
    {       
        switch ($mode)
        {
            case 'image':        
                $sizes = json_decode(get_input('sizes'));
                return UploadedFile::upload_images_from_input($file_input, $sizes);           
            case 'scribd':
                return UploadedFile::upload_scribd_from_input($file_input);
            default:
                return UploadedFile::upload_from_input($file_input);
        }         
    }   

    function before()
    {
        Session::get_instance()->override_security_check();
        
        Permission_RegisteredUser::require_any();
    }
    
    function do_POST()
    {        
        try
        {  
            $files = $this->upload_file_in_mode($_FILES['file'], get_input('mode'));                    
            $json = UploadedFile::json_encode_array($files);
        }
        catch (Exception $ex)
        {
            $json = json_encode(array(
                'error' => $ex->getMessage()
            ));            
            if (!($ex instanceof DataFormatException) && !($ex instanceof IOException))
            {
                notify_exception($ex);
            }                        
        }
        $this->set_content_type('text/html'); // a lie, but required for plupload's html4 runtime          
        $this->set_content($json);
    }
}    