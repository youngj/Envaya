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

    function process_input()
    {        
        $this->require_login();
        
        try
        {  
            $files = $this->upload_file_in_mode($_FILES['file'], get_input('mode'));        
            $json = UploadedFile::json_encode_array($files);            
        }
        catch (Exception $ex)
        {
            $json = json_encode(array('error' => $ex->getMessage()));
            
            if (!($ex instanceof DataFormatException) && !($ex instanceof IOException))
            {
                notify_exception($ex);
            }                        
        }
                
        if (get_input('iframe'))
        {
            Session::set('lastUpload', $json);
            $this->redirect("/pg/upload?".http_build_query($_POST));
        }
        else
        {
            $this->set_content_type('text/javascript');
            $this->set_content($json);
        }
    }
    
    protected function validate_security_token() {}
    
    function render()
    {
        $this->set_content(view('upload/frame'));
    }
}    