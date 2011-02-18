<?php

foreach ($vars['fields'] as $k => $v)
{
	echo view('input/hidden', array('name' => escape($k), 'value' => $v));
}
