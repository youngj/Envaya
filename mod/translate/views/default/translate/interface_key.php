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
<table class='inputTable gridTable' style='width:600px'>
<tr>
    <th style='width:150px'><?php echo __('itrans:language_key'); ?></th>
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
        $is_stale = $translation->is_stale();
        $style = $is_stale ? 'color:#666' : '';
        echo "<tr><th>";
        echo escape($target_language->name);
        echo "<div style='font-weight:normal'>";
        echo view('translate/translation_score', array('translation' => $translation)); 
        
        if ($translation->can_edit())
        {
            echo "<div class='admin_links'>".
                view('output/confirmlink', array(
                    'href' => $translation->get_url() . "/delete",
                    'text' => __('delete'),
                ))."</div>";
        }        
        echo "</div>";
        echo "</th><td style='$style'>";

        echo view($output_view, array('value' => $translation->value));

        if ($is_stale)
        {
            echo "<div class='help'>".__('itrans:stale')."</div>";
        }
        echo "</td></tr>";
    }
?>
</table>
<br />
    <label><?php echo sprintf(__('itrans:add_in'), escape($target_language->name)); ?></label><br />
<?php
    if (!Session::isloggedin())
    {
        echo "<a href='/pg/login?next=".urlencode($key->get_url())."'>".__('login')."</a>";
    }
    else
    {
?>
<form method='POST' action='<?php echo $key->get_url(); ?>/add'>
<?php echo view('input/securitytoken'); ?>
<?php echo view('input/uniqid'); ?>

<div class='input'>
<?php 
    $tokens = $key->get_placeholders();
    if ($tokens)
    {
        $token_str = implode(' ', array_map(function($t) { return "<strong>$t</strong>"; }, $tokens));
        echo "<div>".__('itrans:needs_placeholders')." $token_str</div>";
    }

    if (strlen($base_value) > 75 || strpos($base_value, "\n") !== FALSE)
    {
       $view = "input/longtext";
       $js = "style='height:".(30+floor(strlen($enText)/50)*25)."px'";
    }
    else
    {
        $view = "input/text";
        $js = '';
    }

    echo view($view, array(
        'name' => 'value',
        'js' => $js,
    )); 
?>
</div>
<?php echo view('input/submit', array('value' => __('trans:submit'))); ?>
</form>
<?php
    }
?>