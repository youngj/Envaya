<?php
    $key = $vars['key'];    
    $translation = $vars['translation']; 
    
    $base_url = Request::get_uri();
    
    $base_value = $key->get_current_base_value(); 
    
    $target_language = $key->get_language();
    
    $query = $key->query_translations()->order_by('time_created desc');        
    $translations = $query->filter(); 

    PageContext::add_header_html("
        <style type='text/css'>
            .translateTable { margin: 0 auto; }
            .translateTable th { padding:4px;font-size:14px;text-align:center; }
            .translateTable td { padding:4px; }
        </style>
    ");
?>
<div style='float:right'>
<ul>
<li><a href='/tr/instructions#key' target='_blank'><?php echo __('itrans:instructions') ?></a></li>
</ul>
</div>

<div style='padding-bottom:5px;text-align:center;'>
<?php  
    echo "<a href='".escape($base_url)."/prev' title='".__('previous')."'><span>&#xab; ".__('previous')."</span></a> &nbsp; &middot; &nbsp; ";
    echo "<a href='".escape($base_url)."/next' title='".__('next')."'><span>".__('next')." &#xbb;</span></a>";
?>
</div>
<table class='translateTable'>
<tr>
    <th><?php 
    
        echo __("itrans:base_lang"); 

        echo " ";
        
        $cur_base_lang = $key->get_current_base_lang();
        $lang_name = escape($cur_base_lang ? __("lang:$cur_base_lang") : __('itrans:unknown'));
        
        if ($key instanceof EntityTranslationKey && $key->can_edit())
        {
            echo "<a href='$base_url/base_lang'>($lang_name)</a>";
        }    
        else
        {
            echo "($lang_name)";
        }
    ?></th>
    <th><?php echo escape($target_language->name); ?></th>
</tr>
<tr> 
    <td>
    <div class='translation'><?php echo $key->view_value($base_value); ?></div>    
    </td>
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
    
        if (!$key->can_edit())
        {                    
            if ($key instanceof EntityTranslationKey || !Config::get('translate:live_interface'))
            {
                echo "<div style='float:right;padding-top:5px;width:220px;color:#999;font-size:11px'>";            
                echo __('itrans:needs_approval');            
                echo "</div>";
            }            
        }
    
        echo view('input/submit', array(
            'style' => 'margin-top:0px',
            'value' => __('itrans:submit')
        )); 
        
        echo "</form>";
    }
    else
    {        
        echo "<div class='translation'>"; 
        if ($displayed_value)
        {
            echo $key->view_value($displayed_value);
        }
        else
        {
            echo "<div style='color:#ccc;padding:4px;'>(".__('itrans:not_translated').")</span>";        
        }
        echo "</div>";    
        echo "<br />";
        
    }        
?>       
    </td>
</tr>
</table>

<?php
    if (!Session::isloggedin())
    {       
        echo "<div style='text-align:center;padding-bottom:15px'>";
        echo __('itrans:need_login');
        $next = urlencode($base_url);
        echo " <strong><a href='/pg/login?next=$next'>".__('login')."</a></strong> &middot; ";
        echo "<strong><a href='/pg/register?next=$next'>".__('register')."</a></strong>";
        echo "</div>";
    }        
?>


<table style='margin:0 auto'>
<tr>
<td style='padding-right:10px;width:285px'>
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
        echo "<h3>".__('comment:title')."</h3>";
        foreach ($comments as $comment)
        {
            echo view('translate/key_comment', array('comment' => $comment));
        }
    }
           
    if (Session::isloggedin())
    {    
        echo "<a href='javascript:toggleAddComment()' style='font-weight:bold'>".__('comment:add')."</a>";
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
            'value' => 'all'
        ));
        
        echo view('input/submit', array('value' => __('comment:publish'))); 
        echo "</form>";        
        echo "</div>";
    }    
?>
</td>
<td style='padding-left:5px'>
<?php
    if ($translations)
    {
        echo "<h3>".__('itrans:history')."</h3>";

        echo "<table class='inputTable' style='width:440px'>";
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
                       
            $edit_links = array();
            $edit_links[] = "<a href='".escape($base_url)."?translation={$translation->guid}'>".__('edit')."</a> ";
            
            if ($translation->can_edit())
            {                               
                if ($key->can_edit())
                {
                    if ($translation->is_approved())
                    {
                        $edit_links[] = view('input/post_link', array(
                            'href' => "$translation_url/set_approval?approval=0",
                            'text' => __('itrans:unapprove'),
                        ));
                    }
                    else
                    {
                        $edit_links[] = view('input/post_link', array(
                            'href' => "$translation_url/set_approval?approval=1",
                            'text' => __('itrans:approve'),
                        ));                    
                    }
                }                
                
                $edit_links[] = view('input/post_link', array(
                    'href' => "$translation_url/delete",
                    'confirm' => __('areyousure'),                
                    'text' => __('delete'),
                ));    
            }  
            
            echo "<div class='admin_links'>";
            echo implode('<br />', $edit_links);
            echo "</div>";
            
            echo "</div>";
            echo "</th>";
            echo "<td style='width:300px;padding-bottom:15px'>";

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
</td>
</tr>
</table>