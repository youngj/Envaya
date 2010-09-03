<?php
$report = $vars['report'];
$content = $vars['content'];
$section = $vars['section'];
$num_sections = $report->get_handler()->num_sections;
?>
<script type='text/javascript'>
function setNextSection($section)
{
    document.getElementById('next_section').value = $section;
}
</script>
<?php

echo "<div class='report_section_nav'>";
$links = array();
for ($i = 1; $i <= $num_sections; $i++)
{
    $section_title = __("{$report->get_handler()->report_code}:section{$i}_title");
    if ($section == $i)
    {
        $links[] = "<span>".escape($section_title)."</span> ";
    }
    else
    {
        $links[] = "<a href='javascript:void(0)' onclick='setSubmitted(); setNextSection($i); document.forms[0].submit()'>".
            escape($section_title)."</a> ";            
    }    
}
echo implode(" &middot; ", $links);

echo "</div>";

echo $content;

echo view('input/hidden', array('internalname' => 'section', 'value' => $section)); 

echo view('input/hidden', array(
    'internalname' => 'next_section',
    'internalid' => 'next_section',
    'value' => '',
));

if ($section < $num_sections)
{
    echo view('input/submit', array(
        'internalname' => '_submit',
        'value' => __('report:next_page'), 
        'js' => "onclick='return setSubmitted() && setNextSection(".($section+1).")'"
    ));
}
else
{
    echo view('input/submit', array(
        'internalname' => '_submit',
        'value' => __('report:next_page'), 
        'trackDirty' => true
    ));
}


?>
