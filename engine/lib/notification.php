<?php

    function get_email_fingerprint($email)
    {
        return substr(md5($email . get_site_secret() . "-email"), 0,15);
    }

    function send_mail($mail, $immediate = false)
    {
        if ($immediate)
        {
            return _send_mail_now($mail);
        }
        else
        {
            return FunctionQueue::queue_call('_send_mail_now', array($mail));
        }
    }

    function _send_mail_now($mail)
    {               
        $mailer = Zend::mail_transport();   

        if (!$mail->getFrom())
        {
            $mail->setFrom(Config::get('email_from'), Config::get('sitename'));
        }
        
        $mail->send($mailer);
        return true;
    }

    function send_admin_mail($mail, $immediate = false)
    {
        $mail->addTo(Config::get('admin_email'));    
        return send_mail($mail, $immediate);
    }
