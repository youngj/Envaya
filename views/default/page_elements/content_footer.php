<div id='viewtype'>
<?php
    $viewTypes = array('default', 'mobile');
    $curViewType = Views::get_current_type();
    $links = array();
    
    foreach ($viewTypes as $viewType)
    {
        $text = __("viewtype:$viewType");
    
        if ($viewType == $curViewType)
        {
            $links[] = "<strong>".$text."</strong>";
        }
        else
        {
            $url = url_with_param(Request::full_original_url(), 'view', $viewType);
            $url = url_with_param($url, '__sv','1');
            $links[] = "<a rel='nofollow' href='".escape($url)."'>$text</a>";
        }           
    }
    echo implode(' &middot; ', $links);
?>
</div>
<div id='language'>        
    <?php echo view('page_elements/language_links'); ?>    
</div>
<div style='clear:both;padding:4px;'></div>
<?php echo view('page_elements/footer_menu', $vars); ?>