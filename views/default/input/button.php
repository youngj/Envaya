<?php
    /**
     * Create a input button
     *
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['name'] The name of the input field
     * @uses $vars['type'] Submit or reset, defaults to submit.
     * @uses $vars['src'] Src of an image
     *
     */

    $class = (isset($vars['class'])) ? $vars['class'] : "submit_button";

    if (isset($vars['type'])) { $type = strtolower($vars['type']); } else { $type = 'submit'; }
    switch ($type)
    {
        case 'button' : $type='button'; break;
        case 'reset' : $type='reset'; break;
        case 'submit':
        default: $type = 'submit';
    }

    $value = escape(@$vars['value']);
    $name = @$vars['name'];
    $src = (isset($vars['src'])) ? "src=\"{$vars['src']}\"" : '';

    $setDirty = (@$vars['trackDirty']) ? " onclick='setSubmitted()'" : "";
?>
<button name="<?php echo $name; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?> type="<?php echo $type; ?>" class="<?php echo $class; ?>" <?php echo @$vars['js'], $setDirty; ?> value='1' <?php echo $src; ?> ><div><span><?php echo $value; ?></span></div></button>