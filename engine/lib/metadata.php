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
                    $this->attributes['value'] = json_decode($value);
                }    
                else if ($valueType == 'integer')
                {
                    $this->attributes['value'] = (int)$value;
                }

                $this->attributes['type'] = "metadata";
            }
        }
    }

    public function getEntity() 
    {
        return get_entity($this->entity_guid);
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

        if ($this->id > 0)
        {              
            return update_data("UPDATE metadata set value=?, value_type=? where id=? and name=?",
                array($value, $valueType, $this->id, $name)
            );
        }
        else
        { 
            $this->id = insert_data("INSERT into metadata (entity_guid, name, value, value_type) VALUES (?,?,?,?)", 
                array($this->entity_guid, $name, $value, $valueType)
            );
            
            if (!$this->id)         
            {
                throw new IOException(sprintf(elgg_echo('IOException:UnableToSaveNew'), get_class()));                
            }    
            return $this->id;
        } 			
    }

    /**
     * Delete a given metadata.
     */
    function delete() 
    { 
        return delete_data("DELETE from metadata where id=?", array($this->id));
    }
}

/**
 * Convert a database row to a new ElggMetadata
 *
 * @param stdClass $row
 * @return stdClass or ElggMetadata
 */
function row_to_elggmetadata($row) 
{
    if (!($row instanceof stdClass))
        return $row;

    return new ElggMetadata($row);
}


/**
 * Get a specific item of metadata.
 * 
 * @param $id int The item of metadata being retrieved.
 */
function get_metadata($id)
{
    return row_to_elggmetadata(
        get_data_row("SELECT * FROM metadata WHERE id = ?", array($id))
    );
}

function remove_metadata($entity_guid, $name) 
{	
    return delete_data("DELETE from metadata WHERE entity_guid = ? and name = ?",  array($entity_guid, $name));       
}

function get_metadata_byname($entity_guid, $name)
{
    return row_to_elggmetadata(get_data_row(
        "SELECT * from metadata where entity_guid=? and name=? LIMIT 1", array($entity_guid, $name)
    ));
}

function get_metadata_for_entity($entity_guid)
{
    return array_map('row_to_elggmetadata', get_data(
        "SELECT *, from metadata where entity_guid=?", array($entity_guid)
    ));
}

/**
 * Return a list of entities based on the given search criteria.
 * 
 * @param mixed $meta_name 
 * @param mixed $meta_value
 * @param string $entity_type The type of entity to look for, eg 'site' or 'object'
 * @param string $entity_subtype The subtype of the entity.
 * @param int $limit 
 * @param int $offset
 * @param string $order_by Optional ordering.
 * @param int $site_guid The site to get entities for. Leave as 0 (default) for the current site; -1 for all sites.
 * @param true|false $count If set to true, returns the total number of entities rather than a list. (Default: false)
 * 
 * @return int|array A list of entities, or a count if $count is set to true
 */
function get_entities_from_metadata($meta_name, $meta_value = "", $entity_type = "", $entity_subtype = "", $owner_guid = 0, $limit = 10, $offset = 0, $order_by = "", $site_guid = 0, $count = false)
{						
    $where = array();
    $args = array();

    get_entity_conditions($where, $args, array(
        'type' => $entity_type,
        'subtype' => $entity_subype,
        'owner_guid' => $owner_guid
    ), 'e');

    if ($meta_name!=="")
    {
        $where[] = "m.name=?";
        $args[] = $meta_name;
    }    

    if ($meta_value!=="")
    {
        $where[] = "m.value=?";
        $args[] = $meta_value;
    }    

    if (!$count) 
    {
        $query = "SELECT distinct e.* "; 
    } 
    else 
    {
        $query = "SELECT count(distinct e.guid) as total ";
    }

    $query .= "from entities e JOIN metadata m on e.guid = m.entity_guid where";
    foreach ($where as $w)
    {
        $query .= " $w and ";            
    }    
    $query .= get_access_sql_suffix("e"); 

    if (!$count) 
    {            
        $order_by = sanitize_order_by($order_by);
        if ($order_by == "") 
            $order_by = "e.time_created desc";
        else
            $order_by = "e.time_created, {$order_by}";

        $query .= " order by $order_by";

        if ($limit) 
        {
            $query .= " limit ?, ?"; 
            $args[] = (int)$offset;
            $args[] = (int)$limit;
        }    

        return array_map('entity_row_to_elggstar', get_data($query, $args));
    } 
    else 
    {
        if ($row = get_data_row($query, $args))
            return $row->total;
    }
    return false;
}

/**
 * Return a list of entities suitable for display based on the given search criteria.
 * 
 * @see elgg_view_entity_list
 * 
 * @param mixed $meta_name Metadata name to search on
 * @param mixed $meta_value The value to match, optionally
 * @param string $entity_type The type of entity to look for, eg 'site' or 'object'
 * @param string $entity_subtype The subtype of the entity
 * @param int $limit Number of entities to display per page
 * @param true|false $fullview Whether or not to display the full view (default: true)
 * @param true|false $viewtypetoggle Whether or not to allow users to toggle to the gallery view. Default: true
 * @param true|false $pagination Display pagination? Default: true
 * 
 * @return string A list of entities suitable for display
 */
function list_entities_from_metadata($meta_name, $meta_value = "", $entity_type = "", $entity_subtype = "", $owner_guid = 0, $limit = 10, $fullview = true, $viewtypetoggle = true, $pagination = true) {

    $offset = (int) get_input('offset');
    $limit = (int) $limit;
    $count = get_entities_from_metadata($meta_name, $meta_value, $entity_type, $entity_subtype, $owner_guid, $limit, $offset, "", 0, true);
    $entities = get_entities_from_metadata($meta_name, $meta_value, $entity_type, $entity_subtype, $owner_guid, $limit, $offset, "", 0, false);

    return elgg_view_entity_list($entities, $count, $offset, $limit, $fullview, $viewtypetoggle, $pagination);

}

/**
 * Clear all the metadata for a given entity, assuming you have access to that metadata.
 * 
 * @param int $guid
 */
function clear_metadata($entity_guid)
{
    return delete_data("DELETE from metadata where entity_guid=?", array($entity_guid));
}

function clear_metadata_by_owner($owner_guid)
{
    return delete_data("DELETE from metadata WHERE owner_guid=?", array($owner_guid));
}	

function string_to_tag_array($string) 
{
    if (is_string($string)) {
        $ar = explode(",",$string);
        $ar = array_map('trim', $ar); // trim blank spaces
        $ar = array_map('elgg_strtolower', $ar); 
        $ar = array_filter($ar, 'is_not_null'); // Remove null values
        return $ar;
    }
    return false;
}

function detect_value_type($value)
{
    if (is_array($value))
        return 'json';
    if (is_int($value)) 
        return 'integer';
    if (is_numeric($value)) 
        return 'text'; // todo?
    return 'text';
}