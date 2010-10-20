<?php echo get_submenu_group('footer', 'canvas_header/link_submenu', 'canvas_header/footer_submenu_group'); ?>
<div class='language'>        
    <?php echo view('page_elements/language_links'); ?>    
</div>
<div class='language'>        
    <?php
        $viewTypes = array('default', 'mobile');
        $curViewType = get_viewtype() ?: 'default';
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
                $links[] = "<a href='".escape($url)."'>$text</a>";
            }           
        }
        echo implode(' &middot; ', $links);
    ?>
    
</div>
