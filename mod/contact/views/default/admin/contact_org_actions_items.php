<?php

$org = $vars['org'];

if ($org->email)
{
    echo "<a href='/admin/contact/email/user/{$org->guid}'>".__('contact:send_email')."</a>";
}
