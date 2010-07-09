<?php

$lang = $vars['lang'];

$query = get_input('q');

$keys = get_translatable_language_keys();

if ($query)
{
    $lq = strtolower($query);

    $filteredKeys = array();
    foreach ($keys as $key)
    {
        if (strpos($key, $lq) !== false
            || strpos(strtolower(elgg_echo($key, 'en')), $lq) !== false
            || strpos(strtolower(elgg_echo($key, $lang)), $lq) !== false)
        {
            $filteredKeys[] = $key;
        }
    }
    $keys = $filteredKeys;
}

$edited = get_input('edited');

if ($edited)
{
    $filteredKeys = array();

    $editedKeys = array();
    foreach (InterfaceTranslation::filterByLang($lang) as $itrans)
    {

        if ($itrans->value != @$CONFIG->translations[$lang][$itrans->key])
        {
            $editedKeys[$itrans->key] = true;
        }
    }

    foreach ($keys as $key)
    {
        if (@$editedKeys[$key])
        {
            $filteredKeys[] = $key;
        }
    }

    $keys = $filteredKeys;
}

$limit = 10;
$baseurl = "translate.php?q=".urlencode($query)."&edited=".($edited ? 1 : 0);
$offset = (int)get_input('offset');
$count = sizeof($keys);

$from = urlencode("$baseurl&offset=$offset");

echo "<form method='GET' action='translate.php'>";

echo "<label>".elgg_echo("trans:filter")."</label><br />";

echo elgg_view('input/text', array('internalname' => 'q', 'value' => $query));
echo elgg_view('input/submit', array('value' => elgg_echo("search")));
echo "<div class='edited'>";
echo elgg_view('input/checkboxes', array(
    'internalname' => 'edited',
    'options' => array('1' => elgg_echo('trans:edited_only')),
    'value' => $edited ? '1' : null
));
echo "</div>";

echo "</form>";

echo "<br />";
echo "<h3>";
if ($query)
{
    echo sprintf(elgg_echo("trans:search"), escape($query));
}
echo "</h3>";

if (empty($keys))
{
    echo elgg_echo("search:noresults");
}
else
{
    echo elgg_view('navigation/pagination',array(
        'baseurl' => $baseurl,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));

    echo "<table class='gridTable'>";

    echo "<tr>";
    echo "<th>".elgg_echo('trans:key')."</th>";
    echo "<th>".escape(elgg_echo('en'))."</th>";
    echo "<th>".escape(elgg_echo($lang))."</th>";

    for ($i = $offset; $i < $offset + $limit && $i >= 0 && $i < $count; $i++)
    {
        $key = $keys[$i];

        $enText = elgg_echo($key, 'en');

        echo "<tr>";
        echo "<td>".escape($key)."</td>";
        echo "<td>".elgg_view('output/longtext', array('value' => $enText))."</td>";

        $trans = @$CONFIG->translations[$lang][$key];

        $it = InterfaceTranslation::getByKeyAndLang($key, $lang);

        if ($it)
        {
            $val = elgg_view('output/longtext', array('value' => $it->value));
            if ($trans != $it->value)
            {
                $res = "<div class='edited'>$val</div>";
            }
            else
            {
                $res = "<div class='reviewed'>$val</div>";
            }
        }
        else if ($trans)
        {
            $res = elgg_view('output/longtext', array('value' => $trans));
        }
        else
        {
            $res = "<em>".elgg_echo("trans:none")."</em>";
        }

        echo "<td>$res</td>";
        echo "<td><a href='translate.php?key=$key&from=$from'>".elgg_echo('edit')."</a></td>";

        echo "</tr>";
    }

    echo "</table>";

    echo elgg_view('navigation/pagination',array(
        'baseurl' => $baseurl,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));

    if (isadminloggedin())
    {
        echo "<br /><br /><a href='translate.php?export=1'>".elgg_echo('trans:export')."</a>";
    }
}
