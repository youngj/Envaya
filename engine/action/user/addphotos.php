<?php

class Action_User_AddPhotos extends Action
{
    function before()
    {
        Permission_EditUserSite::require_for_entity($this->get_user());
    }
     
    function process_input()
    {
        $imageNumbers = Input::get_array('imageNumber');
        
        $uniqid = Input::get_string('uniqid');
        $user = $this->get_user();
        
        $news = Widget_News::get_or_init_for_entity($user);
        
        $duplicate = Session::get_entity_by_uniqid($uniqid);
        if ($duplicate)
        {
            throw new RedirectException('', $duplicate->get_url());
        }
        
        foreach ($imageNumbers as $imageNumber)
        {                        
            $imageData = Input::get_string('imageData'.$imageNumber);
            
            if (!$imageData) // mobile version uploads image files when the form is submitted, rather than asynchronously via javascript
            {     
                $sizes = json_decode(Input::get_string('sizes'));
                
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
            
            $imageCaption = Input::get_string('imageCaption'.$imageNumber);
            
            $image = $images[sizeof($images) - 1];
            
            $body = "<p><img class='image_center' src='{$image->get_url()}' width='{$image->width}' height='{$image->height}' /></p>";
            if ($imageCaption)
            {
                $body .= "<p>".view('input/longtext', array('value' => $imageCaption))."</p>";
            }
                        
            $post = Widget_Post::new_for_entity($news);
            $post->set_owner_entity(Session::get_logged_in_user());
            $post->set_content($body);
            $post->save();              
            $post->post_feed_items();
            
            Session::cache_uniqid($uniqid, $post);
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