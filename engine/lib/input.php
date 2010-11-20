<?php     
    function get_input($variable, $default = "")
    {
        return (isset($_REQUEST[$variable])) ? $_REQUEST[$variable] : $default;
    }

    function get_input_array($variable)
    {
        $res = get_input($variable);
        if (is_array($res))
        {
            return $res;
        }
        else if ($res != null)
        {
            return array($res);
        }
        else
        {
            return array();
        }
    }

	function get_bit_field_from_options($options)
	{
		$field = 0;
		foreach ($options as $item)
		{
			$field |= (int)$item;
		}
		return $field;
	}