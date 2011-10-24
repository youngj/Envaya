<?php

$relationship = $vars['relationship'];



$contact = array();

if (!$relationship->subject_guid)
{
    if ($relationship->subject_email)
    {
        $contact[] = view('output/email', array('value' => $relationship->subject_email));
    }
 
    if ($relationship->subject_phone)
    {
        $contact[] = view('output/text', array('value' => $relationship->subject_phone));
    }
}

if (sizeof($contact))
{
    echo '<div>'.implode(' &middot; ', $contact).'</div>';
}
