<?php
    $languages = InterfaceLanguage::query()->order_by('name')->filter();
?>

<?php echo view('section', array('header' => __('itrans:languages'))); ?>
<div class='section_content padded'>
<?php
    foreach ($languages as $language)
    {
        echo "<a href='/tr/admin/{$language->code}'>".escape($language->get_title())."</a><br />";
    }
?>
</div>

<?php echo view('section', array('header' => __('itrans:add_language'))); ?>
<div class='section_content padded'>
<form method='GET' action='' onsubmit='goLang()'>
<script type='text/javascript'>
function goLang()
{
    var form = document.forms[0];
    form.action = "/tr/admin/" + form.code.value;
}
</script>
<?php echo __('itrans:language_code'); ?>: 
<?php 
echo view('input/text', array('name' => 'code', 'style' => "width:50px"));
echo view('input/submit', array('value' => __('go')));
?>
</form>
</div>