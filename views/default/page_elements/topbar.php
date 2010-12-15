<div id="topbar">
<table class='topbarTable'>
<tr>
<td class='topbarLinks'>
    <a id='logoContainer' href="/home">
        <img src="/_graphics/logo.gif?v5" alt="Envaya" width="145" height="30">
    </a>
    <a href='/envaya'><?php echo __('about') ?></a>
    <a href='/org/browse'><?php echo __('browse') ?></a>
    <a href='/org/search'><?php echo __('search') ?></a>
    <a href='/org/feed'><?php echo __('feed') ?></a>    
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
            $url = url_with_param(Request::full_original_url(), 'lang', $lang);
            $translationUrls[$url] = $text;
            
            if ($curLang == $lang)
            {
                $curUrl = $url;
            }            
        }
        
        echo view('input/pulldown', array(
            'internalname' => 'top_language',
            'internalid' => 'top_language',
            'options' => $translationUrls,
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
<div id='topRight'><?php echo view('page_elements/login_area', $vars); ?></div>
<?php } ?>

</div>
<div style="clear:both"></div>