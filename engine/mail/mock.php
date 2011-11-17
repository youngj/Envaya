<?php

Zend::load('Zend_Mail_Transport_Abstract');

class Mail_Mock extends Zend_Mail_Transport_Abstract
{
    protected function _sendMail()
    {
        $file = fopen(Config::get('mail:mock_file'), 'a');
        if (!$file)
        {
            return;
        }
        fwrite($file, "========\n");
        fwrite($file, $this->header . $this->EOL . $this->body);
        fwrite($file, "\n--------\n");
        fclose($file);
    }
}