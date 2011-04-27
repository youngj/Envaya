<?php
    $languages = InterfaceLanguage::query()->order_by('name')->filter();
?>
<table class='gridTable' style='width:300px'>
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