<?php
    $loginUrl = $vars['login_url'] ?: '/pg/login';
    echo "<a href='".escape($loginUrl)."'>".__("login")."</a>";
