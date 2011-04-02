<?php

class Controller_Pg extends Controller {

    function action_login()
    {
        $action = new Action_Login($this);
        $action->execute();   
    }
    
    function action_logout()
    {
        logout();
        forward();
    }    
    
    function action_tci_donate_frame()
    {
        $this->request->response = view("page/tci_donate_frame", $values);
    }

    function action_submit_donate_form()
    {
        $values = $_POST;
        $amount = (int)$values['_amount'] ?: (int)$values['_other_amount'];
        $values['donation'] = $amount;

        $emailBody = "";

        foreach ($values as $k => $v)
        {
            $emailBody .= "$k = $v\n\n";
        }

        send_admin_mail(Zend::mail("Donation form started", $emailBody));

        if (!$amount)
        {
            action_error("Please select a donation amount.");
        }
        if (!$values['Name'])
        {
            action_error("Please enter your Full Name.");
        }
        if (!$values['phone'])
        {
            action_error("Please enter your Phone Number.");
        }
        if (!$values['Email'])
        {
            action_error("Please enter your Email Address.");
        }

        unset($values['_amount']);
        unset($values['_other_amount']);
        unset($values['Submit']);

        $this->request->response = view("page/submit_tci_donate_form", $values);
    }

    function action_dashboard()
    {
        $this->require_login();
        forward(Session::get_loggedin_user()->get_url()."/dashboard");
    }

    function action_forgot_password()
    {
        $action = new Action_ForgotPassword($this);
        $action->execute();
    }

    function action_password_reset()
    {
        $action = new Action_PasswordReset($this);
        $action->execute();    
    }

    function action_upload()
    {
        $action = new Action_Upload($this);
        $action->execute();
    }

    function action_send_feedback()
    {
        $message = get_input('message');
        $from = get_input('name');
        $email = get_input('email');
        
        if (!$message)
        {
            action_error(__('feedback:empty'));
        }
        
        if (!$email)
        {
            action_error(__('feedback:email_empty'));
        }

        try
        {
            validate_email_address($email);
        }
        catch (RegistrationException $ex)
        {
            action_error($ex->getMessage());
        }
        
        $mail = Zend::mail("User feedback", "From: $from\n\nEmail: $email\n\n$message");
        $mail->setReplyTo($email);
        
        send_admin_mail($mail);
        system_message(__('feedback:sent'));
        forward("/");
    }

    function action_large_img()
    {
        $owner_guid = get_input('owner');
        $group_name = get_input('group');

        $largeFile = UploadedFile::query()->where('owner_guid = ?', $owner_guid)->where('group_name = ?', $group_name)
            ->order_by('width desc')->get();

        if ($largeFile)
        {
            echo "<html><body><img src='{$largeFile->get_url()}' width='{$largeFile->width}' height='{$largeFile->height}' /></body></html>";
        }
        else
        {
            not_found();
        }
    }

    function action_receive_sms()
    {
        $from = @$_REQUEST['From'];
        $body = @$_REQUEST['Body'];

        error_log("SMS received:\n from=$from body=$body");

        if ($from && $body)
        {
            $sms_request = new SMS_Request($from, $body);
            $sms_request->execute();
        }
        else
        {
            not_found();
        }
    }

    function action_delete_comment()
    {
        $guid = (int)get_input('comment');
        $comment = Comment::query()->where('e.guid=?', $guid)->get();
        if ($comment && $comment->can_edit())
        {
            $comment->disable();
            $comment->save();

            $container = $comment->get_container_entity();
            $container->num_comments = $container->query_comments()->count();
            $container->save();

            system_message(__('comment:deleted'));
        }
        else
        {
            register_error(__('comment:not_deleted'));
        }
        forward_to_referrer();
    }

    function action_local_store()
    {
        // not for use in production environment
        $storage_local = get_storage();

        if (!($storage_local instanceof Storage_Local))
        {
            return not_found();
        }

        $path = get_input('path');

        $components = explode('/', $path);

        foreach ($components as $component)
        {
            if (preg_match('/[^\w\.\-]|(\.\.)/', $component))
            {
                return not_found();
            }
        }

        $local_path = $storage_local->get_file_path(implode('/', $components));

        if (!is_file($local_path))
        {
            return not_found();
        }

        $mime_type = UploadedFile::get_mime_type($local_path);
        if ($mime_type)
        {
            header("Content-Type: $mime_type");
        }
        echo file_get_contents($local_path);
        exit;
    }
    
    function action_hide_todo()
    {
        Session::set('hide_todo', 1);
        
        $this->request->headers['Content-Type'] = 'text/javascript';
        $this->request->response = json_encode("OK");    
    }
    
    function action_change_lang()
    {
        $url = @$_GET['url'];
        $newLang = $_GET['lang'];
        // change_viewer_language($newLang); // unnecessary because done in start.php
        Session::save_input();
        forward(url_with_param($url, 'lang', $newLang));
    }
    
    function action_js_revision_content()
    {
        $this->request->headers['Content-Type'] = 'text/javascript';                
        
        $id = (int)get_input('id');
        
        $revision = ContentRevision::query()->where('id = ?', $id)->get();
        if (!$revision || !$revision->can_edit())
        {
            throw new SecurityException("Access denied.");
        }
        
        $this->request->response = json_encode(array(
            'content' => $revision->content
        ));
    }
    
    function action_js_revisions()
    {
        $this->request->headers['Content-Type'] = 'text/javascript';                
        
        $entity_guid = (int)get_input('entity_guid');
        
        $entity = get_entity($entity_guid, true);
        if (!$entity)
        {
            $revisions = array();
        }
        else
        {
            if (!$entity->can_edit())
            {
                throw new SecurityException("Access denied.");
            }
            
            $revisions = ContentRevision::query()->where('entity_guid = ?', $entity_guid)->order_by('time_updated desc')->filter();        
        }
        
        $this->request->response = json_encode(array(
            'revisions' => array_map(function($r) { return $r->js_properties(); }, $revisions),
        ));
    }

    function action_select_image()
    {
        $file = UploadedFile::get_from_url(get_input('src'));

        $this->page_draw('',view('upload/select_image',
            array(
                'current' => $file,
                'position' => get_input('pos'),
                'frameId' => get_input('frameId'),
            )
        ), array('no_top_bar' => true));
    }
    
    function action_select_document()
    {
        $guid = (int)get_input('guid');
        $file = ($guid) ? UploadedFile::query()->where('e.guid = ?', $guid)->get() : null;
        
        $this->page_draw('',view('upload/select_image',
            array(
                'current' => $file,
                'frameId' => get_input('frameId'),
            )
        ),  array('no_top_bar' => true));
    }    
}