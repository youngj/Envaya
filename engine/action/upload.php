<?php

class Action_Upload extends Action
{
    protected function upload_file_in_mode($file_input, $mode)
    {   
        if (!$file_input || $file_input['error'] != 0)
        {    
            $error_code = $file_input ? get_constant_name($file_input['error'], 'UPLOAD_ERR') : 'UPLOAD_ERR_NO_FILE';
            throw new IOException(__("upload:transfer_error").": $error_code");
        }    
    
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
        }
                
        if (get_input('iframe'))
        {
            Session::set('lastUpload', $json);
            $this->redirect("pg/upload?".http_build_query($_POST));
        }
        else
        {
            $this->set_content_type('text/javascript');
            $this->set_response($json);
        }
    }
    
    protected function validate_security_token() {}
    
    function render()
    {
        $this->get_request()->response = view('upload/frame');
    }
}    