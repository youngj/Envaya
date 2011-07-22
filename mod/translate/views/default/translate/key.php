<?php
    $key = $vars['key'];    
    $translation = $vars['translation']; 
    
    $base_url = Request::get_uri();

    $base_lang = $key->get_language()->get_current_base_code();
    
    $base_value = $key->get_value_in_lang($base_lang);
    
    $target_language = $key->get_language();
    
    $query = $key->query_translations()->order_by('time_created desc');        
    $translations = $query->filter();        
?>
<div style='float:left'>
<div class='post_nav' style='padding-bottom:5px;width:700px'>
<?php  
    echo "<a href='".escape($base_url)."/prev' title='".__('previous')."' class='post_nav_prev'><span>&#xab; ".__('previous')."</span></a> ";
    echo "<a href='".escape($base_url)."/next' title='".__('next')."' class='post_nav_next'><span>".__('next')." &#xbb;</span></a>";
?>
</div>
<table class='gridTable'>
<tr>
    <th><?php echo __("lang:$base_lang"); ?></th>
    <th><?php echo escape($target_language->name); ?></th>
</tr>
<tr> 
    <td><div class='padded' style='min-width:300px'><?php echo $key->view_value($base_value); ?></div></td>
    <td>
    <?php
    
    $displayed_value = $translation ? $translation->value : $key->best_translation;
    
    if (Session::isloggedin())
    {
        echo "<form method='POST' action='".escape($base_url)."/add'>";
        echo view('input/securitytoken');        
        
        echo $key->view_input($displayed_value ?: $base_value);
    
        echo "<br />";
        $tokens = $key->get_placeholders();
        if ($tokens)
        {
            $token_str = implode(' ', array_map(function($t) { return "<strong>$t</strong>"; }, $tokens));
            echo "<div>".__('itrans:needs_placeholders')."<br />$token_str</div>";
        }    
    
        echo view('focus', array('name' => 'value')); 
    
        echo view('input/submit', array('value' => __('trans:submit'))); 
        echo "</form>";
    }
    else
    {        
        if ($displayed_value)
        {
            echo "<div class='padded' style='min-width:300px'>";        
            echo $key->view_value($displayed_value);
            echo "</div>";    
            echo "<br />";
        }
        echo __('itrans:need_login');
        echo "<ul style='font-weight:bold'>";
        $next = urlencode($base_url);
        echo "<li><strong><a href='/pg/login?next=$next'>".__('login')."</a></strong></li>";
        echo "<li><strong><a href='/pg/register?next=$next'>".__('register')."</a></strong></li>";
        echo "</ul>";
    }        
?>       
    </td>
</tr>
</table>

<?php
    if ($translations)
    {
        echo "<br /><h3>".__('itrans:history')."</h3>";

        echo "<table class='inputTable gridTable'>";
        foreach ($translations as $translation)
        {
            $style = $translation->is_approved() ? "background-color:#e0ffe0" : "";
        
            echo "<tr style='$style'>";
            echo "<th style='vertical-align:top'>";
            echo "<div style='font-weight:normal'>";
            echo "<div class='blog_date'>";
            echo $translation->get_owner_link();
            echo "<br />";
            echo friendly_time($translation->time_created);            
            
            echo "</div>";
            echo view('translate/translation_score', array('translation' => $translation)); 
            
            $translation_url = "{$base_url}/{$translation->guid}";
            
            echo "<div class='admin_links'>";
            
            echo "<a href='".escape($base_url)."?translation={$translation->guid}'>".__('edit')."</a> ";
            
            if ($translation->can_edit())
            {                                
                if (Session::isadminloggedin())
                {
                    if ($translation->is_approved())
                    {
                        echo view('input/post_link', array(
                            'href' => "$translation_url/set_approval?approval=0",
                            'text' => __('itrans:unapprove'),
                        ));                                        
                    }
                    else
                    {
                        echo view('input/post_link', array(
                            'href' => "$translation_url/set_approval?approval=1",
                            'text' => __('itrans:approve'),
                        ));                    
                    }
                    echo " ";
                }                
                
                echo view('input/post_link', array(
                    'href' => "$translation_url/delete",
                    'confirm' => __('areyousure'),                
                    'text' => __('delete'),
                ));    
            }  
            echo "</div>";
            
            echo "</div>";
            echo "</th>";
            echo "<td style='width:400px'>";

            echo $key->view_value($translation->value, 500);
            if ($translation->is_stale())
            {
                echo "<div style='color:#666' class='help'>".__('itrans:stale')."</div>";
            }
            echo "</td></tr>";
        }    
        echo "</table>";
    }
?>   
</div>
<div style='float:left;padding-top:30px;padding-left:10px;width:285px'>
<script type='text/javascript'>
function toggleAddComment()
{
    var div = $('add_comment');
    if (div.style.display == 'none')
    {
        div.style.display = 'block';
        $('comment_content').focus();
    }
    else
    {
        div.style.display = 'none';
    }
}
</script>
<?php
    
    $comments = $key->query_comments()->order_by('time_created')->filter();
    if ($comments)
    {
        echo "<h4>".__('comment:title')."</h4>";
        foreach ($comments as $comment)
        {
            echo view('translate/key_comment', array('comment' => $comment));
        }
    }
        
    echo "<ul>";        
    if (Session::isloggedin())
    {    
        echo "<li><a href='javascript:toggleAddComment()'>".__('comment:add')."</a>";
        echo "<div id='add_comment' style='display:none'>";
        echo "<form method='POST' action='{$base_url}/add_comment'>";
        echo view('input/securitytoken');
        echo "<div>".view('input/longtext', array(
            'id' => 'comment_content', 
            'name' => 'content', 
            'style' => "width:250px;height:50px"
        ))."</div>"; 
        echo __('itrans:show_comment_in'). " ";
        echo view('input/pulldown', array(
            'name' => 'scope',
            'options' => array('current' => $target_language->name, 'all' => __('itrans:all_languages')),
            'value' => Session::isadminloggedin() ? 'all' : 'current'
        ));
        
        echo view('input/submit', array('value' => __('comment:publish'))); 
        echo "</form>";        
        echo "</div></li>";

    }
    echo "<li><a href='/tr/instructions#key' target='_blank'>".__('itrans:instructions')."</a></li>";     
    echo "</ul>";
?>
</div>