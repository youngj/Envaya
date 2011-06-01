<?php
    $key = $vars['key'];    

    $base_lang = Language::get_current_code();        
    if ($base_lang == $key->get_language()->code) // no sense translating from one language to itself
    {
        $base_lang = Config::get('language');
    }        
    
    $base_value = __($key->name, $base_lang);
    
    $target_language = $key->get_language();

    $output_view = $key->get_output_view();      
?>
<div style='width:600px;float:left'>
<div class='post_nav' style='padding-bottom:5px;width:600px'>
<?php  
    echo "<a href='{$key->get_url()}/prev' title='".__('previous')."' class='post_nav_prev'><span>&#xab; ".__('previous')."</span></a> ";
    echo "<a href='{$key->get_url()}/next' title='".__('next')."' class='post_nav_next'><span>".__('next')." &#xbb;</span></a>";
?>
</div>
<form method='POST' action='<?php echo $key->get_url(); ?>/add'>
<?php echo view('input/securitytoken'); ?>
<table class='inputTable gridTable'>
<tr>
    <th style='width:200px;'><?php echo __('itrans:language_key'); ?></th>
    <td style='width:450px'><?php echo escape($key->name); ?></td>
</tr>
<tr style='border-bottom:1px solid gray'>
    <th><?php echo __("lang:$base_lang"); ?></th>
    <td><?php echo view($output_view, array('value' => $base_value)); ?></td>
</tr>

<?php
    $query = $key->query_translations()->order_by('score desc, guid desc');        
    $translations = $query->filter();
    
    foreach ($translations as $translation)
    {
        echo "<tr><th>";
        echo escape($target_language->name);
        echo "<div style='font-weight:normal'>";
        echo "<div class='blog_date'>";
            $date = friendly_time($translation->time_created);
        
            echo strtr(__('date:date_name'), array(
                '{date}' => $date,
                '{name}' => $translation->get_owner_link()
            ));
        
        echo "</div>";
        echo view('translate/translation_score', array('translation' => $translation)); 
        
        if ($translation->can_edit())
        {
            echo "<div class='admin_links'>";
            echo view('input/post_link', array(
                'href' => $translation->get_url() . "/delete",
                'confirm' => __('areyousure'),                
                'text' => __('delete'),
            ));
            echo "</div>";
        }        
        echo "</div>";
        echo "</th><td>";

        echo view($output_view, array('value' => $translation->value));

        if ($translation->is_stale())
        {
            echo "<div style='color:#666' class='help'>".__('itrans:stale')."</div>";
        }
        echo "</td></tr>";
    }
    
    if (Session::isloggedin())
    {
?>
    <tr><th style='vertical-align:top;padding-top:5px'><?php echo sprintf(__('itrans:add_in'), escape($target_language->name)); ?></th>
    <td>
<?php
    if (strlen($base_value) > 75 || strpos($base_value, "\n") !== FALSE)
    {
       $view = "input/longtext";
       $style = "height:".(25+floor(strlen($base_value)/75)*25)."px;width:400px";
    }
    else
    {
        $view = "input/text";
        $style = 'width:400px';
    }

    echo view($view, array(
        'name' => 'value',
        'style' => $style,
    )); 
    echo "<br />";
    $tokens = $key->get_placeholders();
    if ($tokens)
    {
        $token_str = implode(' ', array_map(function($t) { return "<strong>$t</strong>"; }, $tokens));
        echo "<div>".__('itrans:needs_placeholders')."<br />$token_str</div>";
    }    
    
    if (sizeof($translations < 4))
    {    
        echo view('focus', array('name' => 'value')); 
    }
    
    echo view('input/submit', array('value' => __('trans:submit'))); 
?>
</td></tr>
<?php
    }
?>
</table>
</form>
<?php

    if (!Session::isloggedin())
    {
        echo "<br />";
        echo __('itrans:need_login');
        echo "<ul style='font-weight:bold'>";
        $next = urlencode($key->get_url());
        echo "<li><strong><a href='/pg/login?next=$next'>".__('login')."</a></strong></li>";
        echo "<li><strong><a href='/pg/register?next=$next'>".__('register')."</a></strong></li>";
        echo "</ul>";
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
            echo view('translate/interface_key_comment', array('comment' => $comment));
        }
    }
        
    echo "<ul>";        
    if (Session::isloggedin())
    {    
        echo "<li><a href='javascript:toggleAddComment()'>".__('comment:add')."</a>";
        echo "<div id='add_comment' style='display:none'>";
        echo "<form method='POST' action='{$key->get_url()}/add_comment'>";
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