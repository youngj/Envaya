<?php

class SMS_Controller_Unknown extends SMS_Controller
{
    public function execute($message)
    {
        $this->reply("Sorry, this phone number is not configured to receive SMS messages.");
        return $this;
    }
}
