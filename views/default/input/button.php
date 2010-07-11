<?php
    /**
     * Create a input button
     * Use this view for forms rather than creating a submit/reset button tag in the wild as it provides
     * extra security which help prevent CSRF attacks.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     *
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['internalname'] The name of the input field
     * @uses $vars['type'] Submit or reset, defaults to submit.
     * @uses $vars['src'] Src of an image
     *
     */

    global $CONFIG;

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
    $name = @$vars['internalname'];
    $src = (isset($vars['src'])) ? "src=\"{$vars['src']}\"" : '';

    $setDirty = (@$vars['trackDirty']) ? " onclick='setSubmitted()'" : "";
?>
<button name="<?php echo $name; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> type="<?php echo $type; ?>" class="<?php echo $class; ?>" <?php echo $vars['js'], $setDirty; ?> value='1' <?php echo $src; ?> ><div><span><?php echo $value; ?></span></div></button>