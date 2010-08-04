<?php

    $user = get_loggedin_user();

    if ($user instanceof Organization)
    {
        echo view("org/dashboard", array('org' => $user));
    }
    else if ($user->admin)
    {
        ?>
        <div class='padded'>
        <ul>
        <li><a href='admin/contact'>List of Organizations</a></li>
        <li><a href='admin/statistics'>Statistics</a></li>
        <li><a href='admin/user'>User Administration</a></li>
        <li><a href='admin/logbrowser'>Log Browser</a></li>
        </ul>
        </div>
        <?php
    }
    else
    {
        echo "<div class='padded'>You are not an organization!</div>";
    }


?>