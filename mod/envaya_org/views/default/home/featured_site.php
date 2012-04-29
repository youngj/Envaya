<?php
    $widget_guid = State::get('home_bottom_left_guid');
    $widget = $widget_guid ? Widget::get_by_guid($widget_guid) : null;

    if ($widget)
    {
        $title_html = escape($widget->get_title());
        $content_html = $widget->render_content();
    }    
    else
    {
        $title_html = __('featured:home_heading');
        
        $content_html = '';
        $activeSite = FeaturedSite::get_active();
        if ($activeSite)
        {
            $content_html .= view('org/featured_site', array('featured_site' => $activeSite, 'show_date' => false));
        }
        
        $content_html .= "<a class='home_more' href='/org/featured'>".__('featured:see_all')."</a><div style='clear:both'></div>";
        
    }
    echo "<div class='home_featured'>";
    echo "<h4 class='home_featured_heading'>$title_html</h4>";
    echo "<div class='home_featured_content'>$content_html</div>";
    echo "</div>";
