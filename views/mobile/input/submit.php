<?php
	/**
	 * A submit input button, using an input type='submit' instead of a submit element
	 */

    $attrs = Markup::get_attrs($vars, array(
        'class' => 'submit_button',
        'type' => 'submit',
        'name' => null,
        'style' => null,
        'id' => null,
        'value' => null
    ));    
    echo "<input ".Markup::render_attrs($attrs)." />";