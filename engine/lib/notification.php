<?php

    function get_email_fingerprint($email)
    {
        return substr(md5($email . get_site_secret() . "-email"), 0,15);
    }

    function send_mail($to, $subject, $message, $headers = null, $immediate = false)
    {
        global $CONFIG;

        if (!$headers)
        {
            $headers = array();
        }

        if (!isset($headers['From']))
        {
            $headers['From'] = "\"{$CONFIG->sitename}\" <{$CONFIG->email_from}>";
        }
        if (!isset($headers['To']))
        {
            $headers['To'] = $to;
        }

        $subject = preg_replace("/(\r\n|\r|\n)/", " ", $subject); // Strip line endings

        $headers['Subject'] = mb_encode_mimeheader($subject,"UTF-8", "B");

        if (!isset($headers['Content-Type']))
        {
            $headers["Content-Type"] = "text/plain; charset=UTF-8; format=flowed";
        }
        $headers["MIME-Version"] = "1.0";
        $headers["Content-Transfer-Encoding"] = "8bit";

        $message = wordwrap(preg_replace("/(\r\n|\r)/", "\n", $message)); // Convert to unix line endings in body

        if ($immediate)
        {
            return _send_mail_now($to, $headers, $message);
        }
        else
        {
            return FunctionQueue::queue_call('_send_mail_now', array($to, $headers, $message));
        }
    }

    function _send_mail_now($to, $headers, $message)
    {
        $mailer = get_smtp_mailer();
        echo $mailer->send($to, $headers, $message);
        return true;
    }

    function send_admin_mail($subject, $message, $headers  = null, $immediate = false)
    {
        global $CONFIG;
        return send_mail($CONFIG->admin_email, $subject, $message, $headers, $immediate);
    }
	
	function get_mock_mail_file()
	{
		global $CONFIG;
		return getenv("MOCK_MAIL_FILE") ?: $CONFIG->mock_mail_file;
	}

    function mock_send_mail($mail, $recipients, $headers, $body)
    {
        global $CONFIG;
		echo get_mock_mail_file()."\n";
        $file = fopen(get_mock_mail_file(), 'a');
        fwrite($file, "========\n");
        foreach ($headers as $k => $v)
        {
            fwrite($file, "$k: $v\n");
        }
        fwrite($file, "\n");
        fwrite($file, "$body\n\n");
        fwrite($file, "--------\n");
        fclose($file);
    }

    function get_smtp_mailer()
    {
        static $mailer;

        if (!isset($mailer))
        {
            global $CONFIG;           

            if (get_mock_mail_file())
            {
                $mailer = new Mail_mock(array(
                    'postSendCallback' => 'mock_send_mail',
                ));
            }
            else
            {
                $mailer = new Mail_smtp(array(
                    'host' => $CONFIG->smtp_host,
                    'port' => $CONFIG->smtp_port,
                    'username' => 'web@envaya.org',
                    'auth' => true,
                    'password' => $CONFIG->email_pass));
            }
        }
        return $mailer;
    }
