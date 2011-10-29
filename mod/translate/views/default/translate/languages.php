<?php
    $languages = TranslationLanguage::query()->order_by('name')->filter();
?>
<div style='float:left;width:300px;margin-right:20px'>
<table class='gridTable'>
<?php
    echo "<tr>";
    echo "<th>".__('language')."</th>";
    echo "</tr>";

    foreach ($languages as $language)
    {
        echo "<tr>";
        echo "<td style='font-weight:bold'><a href='{$language->get_url()}'>".escape($language->name)." (".escape($language->code).")</a></td>";
        echo "</tr>";
    }
?>
</table>
</div>
<div style='float:left'>
<ul style='font-weight:bold'>
<?php 
    echo "<li><a href='/tr/instructions'>".__('itrans:instructions')."</a></li>"; 
    if (!Session::is_logged_in())
    {
        echo "<li><a href='/pg/register?next=/tr'>".__('register')."</a></li>";     
        echo "<li><a href='/pg/login?next=/tr'>".__('login')."</a></li>";     
    }
    if (Permission_ManageLanguage::has_for_root())
    {
        echo "<li><a style='color:red' href='/tr/admin'>".__('admin')."</a></li>";         
    }
?>
</ul>
</div>