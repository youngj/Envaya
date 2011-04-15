<?php

$lang = $vars['lang'];

$query = get_input('q');

$language = Language::get($lang);
$language->load_all();

$keys = array_keys($language->get_loaded_translations());

if ($query)
{
    $lq = strtolower($query);

    $filteredKeys = array();
    foreach ($keys as $key)
    {
        if (strpos($key, $lq) !== false
            || strpos(strtolower(__($key, 'en')), $lq) !== false
            || strpos(strtolower(__($key, $lang)), $lq) !== false)
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
    foreach (InterfaceTranslation::filter_by_lang($lang) as $itrans)
    {
        if ($itrans->value != $language->get_translation($itrans->key))
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
$baseurl = "/tr/translate_interface?q=".urlencode($query)."&edited=".($edited ? 1 : 0);
$offset = (int)get_input('offset');
$count = sizeof($keys);

$from = urlencode("$baseurl&offset=$offset");

echo "<form method='GET' action='/tr/translate_interface'>";

echo "<label>".__("trans:filter")."</label><br />";

echo view('input/text', array('name' => 'q', 'value' => $query));
echo view('input/submit', array('value' => __("search")));
echo "<div class='edited'>";
echo view('input/checkboxes', array(
    'name' => 'edited',
    'options' => array('1' => __('trans:edited_only')),
    'value' => $edited ? '1' : null
));
echo "</div>";

echo "</form>";

echo "<br />";
echo "<h3>";
if ($query)
{
    echo sprintf(__("trans:search"), escape($query));
}
echo "</h3>";

if (empty($keys))
{
    echo __("search:noresults");
}
else
{
    echo view('pagination',array(
        'baseurl' => $baseurl,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));

    echo "<table class='gridTable'>";

    echo "<tr>";
    echo "<th>".__('trans:key')."</th>";
    echo "<th>".escape(__('en'))."</th>";
    echo "<th>".escape(__($lang))."</th>";

    for ($i = $offset; $i < $offset + $limit && $i >= 0 && $i < $count; $i++)
    {
        $key = $keys[$i];

        $enText = __($key, 'en');

        echo "<tr>";
        echo "<td>".escape($key)."</td>";
        echo "<td>".view('output/longtext', array('value' => $enText))."</td>";

        $trans = Language::get($lang)->get_translation($key);

        $it = InterfaceTranslation::get_by_key_and_lang($key, $lang);

        if ($it)
        {
            $val = view('output/longtext', array('value' => $it->value));
            if ($trans != $it->value)
            {
                $res = "<div style='color:red'>$val</div>";
            }
            else
            {
                $res = "<div style='color:green'>$val</div>";
            }
        }
        else if ($trans)
        {
            $res = view('output/longtext', array('value' => $trans));
        }
        else
        {
            $res = "<em>".__("trans:none")."</em>";
        }

        echo "<td>$res</td>";
        echo "<td><a href='/tr/translate_interface?key=$key&from=$from'>".__('edit')."</a></td>";

        echo "</tr>";
    }

    echo "</table>";

    echo view('pagination',array(
        'baseurl' => $baseurl,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));

    if (Session::isadminloggedin())
    {
        echo "<br /><br /><a href='/tr/translate_interface?export=1'>".__('trans:export')."</a>";
    }
}
