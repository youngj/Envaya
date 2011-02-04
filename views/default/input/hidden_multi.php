<?php

foreach ($vars['fields'] as $k => $v)
{
	echo view('input/hidden', array('internalname' => escape($k), 'value' => $v));
}
