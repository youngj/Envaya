<?php
    $loginUrl = $vars['login_url'] ?: '/pg/login';
    echo "<a id='loginButton' href='".escape($loginUrl)."'><span class='loginContent'><img src='/_graphics/lock.gif' height='20' width='20' /><span class='loginText'>".__("login")."</span></span></a>";
