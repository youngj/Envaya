<?php
    $placeholders = $vars['placeholders'];
    if ($placeholders)
    {
        $token_str = implode(' ', array_map(function($t) { return "<strong>$t</strong>"; }, $placeholders));
        echo "<div>".__('itrans:needs_placeholders')."<br />$token_str</div>";
    }    
