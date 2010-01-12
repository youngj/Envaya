

<form method="POST" action="<?php echo $vars['url']; ?>action/changeLanguage">

<?php 
    echo elgg_view('input/securitytoken'); 
?>

<select name='newLang' onchange='this.form.submit()'>
<?php
$selectedLanguage = get_language();

foreach ($CONFIG->translations as $k => $v)
{
    $selected = ($k == $selectedLanguage) ? " selected='selected'" : '';
    echo "<option value='$k'$selected>".elgg_echo($k, $k)."</option>";
}    
?>            
</select>
<noscript><input type='submit' value='Go'></noscript>
</form>

