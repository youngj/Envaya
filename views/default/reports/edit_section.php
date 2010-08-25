<?php
$report = $vars['report'];
$content = $vars['content'];
$section = $vars['section'];
$max_section = $vars['max_section'];
?>
<script type='text/javascript'>
function setNextSection($section)
{
    document.getElementById('next_section').value = $section;
}
</script>
<?php
echo $content;

echo view('input/hidden', array('internalname' => 'section', 'value' => $section)); 

echo view('input/hidden', array(
    'internalname' => 'next_section',
    'internalid' => 'next_section',
    'value' => '',
));

if ($section > 1)
{
    echo view('input/submit', array(
        'internalname' => '_submit',
        'value' => __('report:prev_page'), 
        'js' => "onclick='return setSubmitted() && setNextSection(".($section-1).")'"
    ));
}
if ($section < $max_section)
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
        'value' => __('report:submit'), 
        'trackDirty' => true
    ));
}


?>
