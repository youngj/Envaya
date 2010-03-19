<div id='languages' class='dropdown' style='display:none'>
    <div class='dropdown_title'><?php echo elgg_echo("language:choose"); ?></div>
    <div class='dropdown_content'>
        <?php 
            $translations = get_installed_translations();
            $curLang = get_language();
            foreach ($translations as $lang => $name)
            {
                $class = ($curLang == $lang) ? " dropdown_item_selected" : "";
                echo "<a class='dropdown_item{$class}' href='action/changeLanguage?newLang={$lang}'>$name</a>";
            }
        ?>    
    </div>
</div>
<script type='text/javascript'>
function openChangeLanguage()
{
    var languages = document.getElementById('languages');
    
    if (languages.style.display == 'none')
    {
        var languageButton = document.getElementById('languageButton');
        languages.style.left = languageButton.offsetLeft + "px";
        languages.style.top = (languageButton.offsetTop + 50) + "px";
        languages.style.display = 'block';

        setTimeout(function() {
            addEvent(document.body, 'click', closeChangeLanguage);
        }, 1);    
    }    
}
function closeChangeLanguage()
{
    setTimeout(function() {
        var languages = document.getElementById('languages');
        languages.style.display = 'none';    
        removeEvent(document.body, 'click', closeChangeLanguage);
    }, 1);    
}
</script>
<div id="topbar">
<table class='topbarTable'>
<td class='topbarLinks'>
    <a id='logoContainer' href="<?php echo ((isloggedin()) ? 'pg/dashboard' : 'pg/home') ?>">
        <img src="_graphics/logo.gif" alt="Envaya" width="145" height="30">
    </a>
    <a href='org/browse'><?php echo elgg_echo('browse') ?></a>
    <a href='org/search'><?php echo elgg_echo('search') ?></a>
    <a href='javascript:void(0)' id='languageButton' onclick='openChangeLanguage()'><?php echo elgg_echo('language') ?></a>
</td>    
<td width='166'>&nbsp;</td>
</tr>
</table>

<?php if (get_context() != "login") { ?>
<div id='topRight'>

    <?php            
    
        if (isloggedin())
        {            
            echo "<div id='loggedinArea'><span class='loggedInAreaContent'>";
            
            $user = get_loggedin_user();
            
            if ($user->isSetupComplete())
            {
                echo "<a href='{$user->getURL()}' title=\"".elgg_echo('topbar:your_home')."\"><img src='_graphics/home.gif?v2' height='24' width='25' /></a>";
                
                if ($user instanceof Organization)
                {
                    echo "<a href='pg/dashboard' title=\"".elgg_echo('topbar:edit_site')."\"><img src='_graphics/pencil.gif' height='23' width='22' /></a>";
                }    
                
                echo "<a href='pg/settings/' title=\"".elgg_echo('settings')."\" id='usersettings'><img src='_graphics/settings.gif' height='25' width='25' /></a>";                
            }            

            // The administration link is for admin or site admin users only
            if ($vars['user']->admin) 
            {
                echo "<a href='pg/admin/'><img src='_graphics/admin.gif' height='25' width='24' /></a>";                
            }                    
            
            echo "<a href='action/logout' title=\"".elgg_echo('logout')."\"><img src='_graphics/logout.gif' height='25' width='22' /></a>";
            
            echo "</span>";

            $submenuB = get_submenu_group('b', 'canvas_header/topbar_submenu', 'canvas_header/topbar_submenu_group'); 
            if ($submenuB)
            {
                echo "<div id='edit_submenu'>$submenuB</div>";
            }     
            
            echo "</div>";
        }
        else
        {
            echo "<a id='loginButton' href='pg/login'><span class='loginContent'><img src='_graphics/lock.gif' height='20' width='20' /><span>".elgg_echo("login")."</span></span></a>";
        }   
           
    ?>    
    
</div>

<?php } ?>

</div>

<div class="clearfloat"></div>