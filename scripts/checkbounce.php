<?php

    require_once("scripts/cmdline.php");

    require_once("engine/start.php");
    require_once("vendors/mime_parser.php");
    require_once("vendors/rfc822_addresses.php");

    umask(0);

    global $CONFIG;

    function handle_message($msg)
    {
        $mime = new mime_parser_class;
        $mime->mbox = 0;
        $mime->decode_bodies = 1;
        $mime->ignore_syntax_errors = 1;
        $mime->track_lines = 1;

        $parameters=array(
            'Data'=>$msg
        );

        if (!$mime->Decode($parameters, $decoded))
        {
            throw new DataFormatException("mime error");
        }

        $subject = $decoded[0]["Headers"]["subject:"];

        if ($subject != "Delivery Status Notification (Failure)")
        {
            return;
        }

        if (!$mime->Analyze($decoded[0], $results))
        {
            throw new DataFormatException('MIME message analyse error: '.$mime->error);
        }

        if ($results['Type'] != 'text')
        {
            throw new DataFormatException("could not find text body of email");
        }

        $textEmail = $results['Data'];


        if (!preg_match('/[\w\.]+@[\w\.]+/', $textEmail, $matches))
        {
            throw new DataFormatException("could not find email address in body");
        }

        echo "$matches[0]\n";

        return true;
    }

    $pop3 = new Net_POP3();
    if ($pop3->connect())
    {
        $pop3->login($CONFIG->email_from,$CONFIG->email_pass);

        print_msg("{$pop3->numMsg()} messages in mailbox");

        for($i = 1; $i <= $pop3->numMsg(); $i++)
        {
            //print_msg("  processing message $i");
            try
            {
                handle_message($pop3->getMsg($i));
            }
            catch (DataFormatException $e)
            {
                echo $e->getMessage()."\n";
            }
            catch (InvalidParameterException $e)
            {
                echo $e->getMessage()."\n";
            }

            // $pop3->deleteMsg($i);
        }

    }

    //$pop3->disconnect();
    print_msg("done checking mail");
