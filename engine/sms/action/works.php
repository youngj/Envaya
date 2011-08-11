<?php

class SMS_Action_Works extends SMS_Action
{
    function execute($req)
    {       
        $org = $req->get_org();
        $username = $org ? $org->username : '(unknown)';        
        $req->reply("It works!\nPhone number: {$req->get_phone_number()}\nUsername: {$username}");
    }    
}