<?php

class Action_User_AddPhotos extends Action
{
    function before()
    {
        Permission_EditUserSite::require_for_entity($this->get_user());
    }
     
    function process_input()
    {
        $imageNumbers = get_input_array('imageNumber');
        
        $uniqid = get_input('uniqid');
        $user = $this->get_user();
        
        $news = $user->get_widget_by_class('News');
        if (!$news->guid)
        {
            $news->save();
        }
        
        $duplicates = $news->query_widgets()->with_metadata('uniqid', $uniqid)->filter();
        
        foreach ($imageNumbers as $imageNumber)
        {                        
            $imageData = get_input('imageData'.$imageNumber);
            
            if (!$imageData) // mobile version uploads image files when the form is submitted, rather than asynchronously via javascript
            {     
                $sizes = json_decode(get_input('sizes'));
                
                try
                {
                    $images = UploadedFile::upload_images_from_input($_FILES['imageFile'.$imageNumber], $sizes);
                }
                catch (IOException $ex)
                {
                    throw new ValidationException($ex->getMessage());
                }
                catch (DataFormatException $ex)
                {
                    throw new ValidationException($ex->getMessage());
                }
            }
            else
            {
                $images = UploadedFile::json_decode_array($imageData);
            }
            
            $imageCaption = get_input('imageCaption'.$imageNumber);
            
            $image = $images[sizeof($images) - 1];
            
            $body = "<p><img class='image_center' src='{$image->get_url()}' width='{$image->width}' height='{$image->height}' /></p>";
            if ($imageCaption)
            {
                $body .= "<p>".view('input/longtext', array('value' => $imageCaption))."</p>";
            }
                        
            $post = $news->new_widget_by_class('Post');
            $post->set_owner_entity(Session::get_logged_in_user());
            $post->set_content($body);
            $post->set_metadata('uniqid', $uniqid);
            $post->save();              
            $post->post_feed_items();
        }
        
        SessionMessages::add(__('upload:photos:success'));
        $this->redirect($user->get_url()."/news");
    }

    function render()
    {
        $user = $this->get_user();
        $this->use_editor_layout();        
        $this->page_draw(array(
            'title' => __('upload:photos:title'),
            'content' => view('account/add_photos', array('user' => $user))
        ));        
    }
    
}    