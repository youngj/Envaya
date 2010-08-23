<?php

if (!@$vars['no_top_bar']) {

?>

<div id="topbar">
<table class='topbarTable'>
<tr>
<td class='topbarLinks'>
    <a id='logoContainer' href="home">
        <img src="_graphics/logo.gif?v5" alt="Envaya" width="145" height="30">
    </a>
    <a href='envaya'><?php echo __('about') ?></a>
    <a href='org/browse'><?php echo __('browse') ?></a>
    <a href='org/search'><?php echo __('search') ?></a>
    <a href='org/feed'><?php echo __('feed') ?></a>    
    <div class='top_language'>
    <script type='text/javascript'>
function languageChanged()
{
    setTimeout(function() {
        var languageList = document.getElementById('top_language');
        window.location.href = languageList.options[languageList.selectedIndex].value;
    }, 1);
}
    </script>
    <?php
        echo __('language');
        echo '&nbsp;';
        
        $translationUrls = array();
        $curUrl = null;
        $curLang = get_language();
        foreach (Language::get_options() as $lang => $text)
        {
            $url = url_with_param(Request::instance()->full_original_url(), 'lang', $lang);
            $translationUrls[$url] = $text;
            
            if ($curLang == $lang)
            {
                $curUrl = $url;
            }            
        }
        
        echo view('input/pulldown', array(
            'internalname' => 'top_language',
            'internalid' => 'top_language',
            'options_values' => $translationUrls,
            'value' => $curUrl,
            'js' => "onchange='languageChanged()' onkeypress='languageChanged()'"
        ));
    ?>
    </div>
</td>
<td width='159'>&nbsp;</td>
</tr>
</table>

<?php if (!@$vars['hideLogin']) { ?>
<div id='topRight'>

    <?php

        if (Session::isloggedin())
        {
            echo "<div id='loggedinArea'><span class='loggedInAreaContent'>";

            $user = Session::get_loggedin_user();

            if ($user->is_setup_complete())
            {
                echo "<a href='{$user->get_url()}' title=\"".__('your_home')."\"><img src='_graphics/home.gif?v2' /></a>";

                if ($user instanceof Organization)
                {
                    echo "<a href='{$user->get_url()}/dashboard' title=\"".__('edit_site')."\"><img src='_graphics/pencil.gif?v3' /></a>";
                }

                echo "<a href='{$user->get_url()}/settings' title=\"".__('settings')."\" id='usersettings'><img src='_graphics/settings.gif' /></a>";
            }

            if ($user->admin)
            {
                echo "<a href='{$user->get_url()}/dashboard'><img src='_graphics/admin.gif' height='25' width='24' /></a>";
            }

            echo "<a href='pg/logout' title=\"".__('logout')."\"><img src='_graphics/logout.gif' /></a>";

            echo "</span>";

            $submenuB = get_submenu_group('edit', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group');
            if ($submenuB)
            {
                echo "<div id='edit_submenu'>$submenuB</div>";
            }

            echo "</div>";
        }
        else
        {
            $loginUrl = (@$vars['loginToCurrentPage']) ? url_with_param(Request::instance()->full_rewritten_url(), 'login',1) : 'pg/login';

            echo "<a id='loginButton' href='".escape($loginUrl)."'><span class='loginContent'><img src='_graphics/lock.gif' height='20' width='20' /><span>".__("login")."</span></span></a>";
        }

    ?>

</div>

<?php } ?>

</div>

<div class="clearfloat"></div>

<?php

}

?>
