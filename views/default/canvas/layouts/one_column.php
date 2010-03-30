<?php 

echo @$vars['area3']; 

?>

<div id="thin_column">
    <div id='heading'>
      <?php echo $vars['area1'] ?>  
    </div>

    <?php            
        $submenu = get_submenu_group('topnav', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group'); 
        if (!empty($submenu))
        {
            echo "<div id='site_menu'>$submenu</div>";
        }    
    ?>    
    <div id='content'>
        <div id='content_top'></div>
        <div id='content_mid'>       
            <?php echo elgg_view('translation/control_bar'); ?>
            <?php echo $vars['area2']; ?>
            &nbsp;    
            <div style='clear:both'></div>        
        </div>
        

        <div id='content_bottom'></div>        
    </div>

<?php
    echo get_submenu_group('footer', 'canvas_header/link_submenu', 'canvas_header/footer_submenu_group'); 
?>

<div class='language'>        
    <?php 

        function language_link($lang)
        {
            $name = escape(elgg_echo($lang, $lang));
            
            if (get_language() == $lang)
            {
                return "<strong>$name</strong>";
            }
            else
            {
                return "<a href='action/changeLanguage?newLang={$lang}'>$name</a>";
            }            
        }
        
        $links = array();
        global $config;
        foreach ($CONFIG->translations as $lang => $v)
        {
            $links[] = language_link($lang);    
        }
        echo implode(' &middot; ', $links);
    ?>    
</div>
</div>

