<?php

class ElggMetadata
{
    protected $dirty = false;
    protected $attributes;

    function __construct($id = null)
    {
        $this->attributes = array();

        if (!empty($id))
        {
            if ($id instanceof stdClass) // db row
                $metadata = $id;
            else
                $metadata = get_metadata($id);

            if ($metadata)
            {
                $objarray = (array) $metadata;
                foreach($objarray as $key => $value)
                {
                    $this->attributes[$key] = $value;
                }

                $value = $metadata->value;
                $valueType = $metadata->value_type;

                if ($valueType == 'json')
                {
                    $this->attributes['value'] = json_decode($value, true);
                }
                else if ($valueType == 'integer')
                {
                    $this->attributes['value'] = (int)$value;
                }

                $this->attributes['type'] = "metadata";
            }
        }
    }

    protected function get($name)
    {
        if (isset($this->attributes[$name]))
        {
            return $this->attributes[$name];
        }
        return null;
    }

    protected function set($name, $value)
    {
        $this->attributes[$name] = $value;
        return true;
    }

    function __get($name) {
        return $this->get($name);
    }

    function __set($name, $value) {
        return $this->set($name, $value);
    }

    function save()
    {
        $name = $this->name;
        $value = $this->value;

        if (is_bool($value))
        {
            $value = ($value) ? 1 : 0;
        }

        $valueType = detect_value_type($value);

        if ($valueType == 'json')
        {
            $value = json_encode($value);
        }

    	save_db_row('metadata', 'id', $this->attributes['id'], array(
			'name' => $name,
			'value_type' => $valueType,
			'value' => $value,
			'entity_guid' => $this->entity_guid
    	));
    }

    /**
     * Delete a given metadata.
     */
    function delete()
    {
        return delete_data("DELETE from metadata where id=?", array($this->id));
    }
}
