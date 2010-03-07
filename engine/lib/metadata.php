<?php
	/**
	 * Elgg metadata
	 * Functions to manage object metadata.
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd <info@elgg.com>

	 * @link http://elgg.org/
	 */

	/**
	 * ElggMetadata
	 * This class describes metadata that can be attached to ElggEntities.
	 * 
	 * @author Curverider Ltd <info@elgg.com>
	 * @package Elgg
	 * @subpackage Core
	 */
	class ElggMetadata extends ElggExtender
	{
        protected $dirty = false;   
            
		/**
		 * Construct a new site object, optionally from a given id value or row.
		 *
		 * @param mixed $id
		 */
		function __construct($id = null) 
		{
			$this->attributes = array();
			
			if (!empty($id)) {
				
				if ($id instanceof stdClass)
					$metadata = $id; // Create from db row
				else
					$metadata = get_metadata($id);	
				
				if ($metadata) {
					$objarray = (array) $metadata;
					foreach($objarray as $key => $value) {
						$this->attributes[$key] = $value;
					}
					$this->attributes['type'] = "metadata";
				}
			}
		}
		
		/**
		 * Class member get overloading
		 *
		 * @param string $name
		 * @return mixed
		 */
		function __get($name) {
			return $this->get($name);
		}
		
		/**
		 * Class member set overloading
		 *
		 * @param string $name
		 * @param mixed $value
		 * @return mixed
		 */
		function __set($name, $value) {
			return $this->set($name, $value);
		}

		/**
		 * Determines whether or not the user can edit this piece of metadata
		 *
		 * @return true|false Depending on permissions
		 */
		function canEdit($user_guid = 0) {
			
			if ($entity = get_entity($this->get('entity_guid'))) {
				return $entity->canEditMetadata($this, $user_guid);
			}
			return false;
			
		}
		
		/**
		 * Save matadata object
		 *
		 * @return int the metadata object id
		 */
		function save()
		{
			if ($this->id > 0)
				return update_metadata($this->id, $this->name, $this->value, $this->value_type, $this->owner_guid, $this->access_id);
			else
			{ 
				$this->id = create_metadata($this->entity_guid, $this->name, $this->value, $this->value_type, $this->owner_guid, $this->access_id);
				if (!$this->id) throw new IOException(sprintf(elgg_new('IOException:UnableToSaveNew'), get_class()));
				return $this->id;
			}
			
		}
		
		/**
		 * Delete a given metadata.
		 */
		function delete() 
		{ 
			return delete_metadata($this->id); 
		}
		
		/**
		 * Get a url for this item of metadata.
		 *
		 * @return string
		 */
		public function getURL() { return get_metadata_url($this->id); }
	
		// SYSTEM LOG INTERFACE ////////////////////////////////////////////////////////////

		/**
		 * For a given ID, return the object associated with it.
		 * This is used by the river functionality primarily.
		 * This is useful for checking access permissions etc on objects.
		 */
		public function getObjectFromID($id) { return get_metadata($id); }
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
		global $CONFIG;

		$access = get_access_sql_suffix("e");
		$md_access = get_access_sql_suffix("m");

		return row_to_elggmetadata(
            get_data_row_2("SELECT m.*, n.string as name, v.string as value from metadata m JOIN entities e on e.guid = m.entity_guid JOIN metastrings v on m.value_id = v.id JOIN metastrings n on m.name_id = n.id where m.id=? and $access and $md_access", 
            array((int)$id)
        ));
	}
	
	/**
	 * Removes metadata on an entity with a particular name, optionally with a given value.
	 *
	 * @param int $entity_guid The entity GUID
	 * @param string $name The name of the metadata
	 * @param string $value The optional value of the item (useful for removing a single item in a multiple set)
	 * @return true|false Depending on success
	 */
	function remove_metadata($entity_guid, $name, $value = "") 
    {	
		$query = "DELETE from metadata WHERE entity_guid = ? and name_id = ?";        
        $args = array($entity_guid, add_metastring($name));
        
		if ($value!="")
        {
			$query .= " and value_id = ?";
            $args[] = add_metastring($value);
        }    
		
		return delete_data_2($query, $args);		
	}
	
	/**
	 * Create a new metadata object, or update an existing one.
	 *
	 * @param int $entity_guid
	 * @param string $name
	 * @param string $value
	 * @param string $value_type
	 * @param int $owner_guid
	 * @param int $access_id
	 * @param bool $allow_multiple
	 */
	function create_metadata($entity_guid, $name, $value, $value_type, $owner_guid, $access_id = ACCESS_PRIVATE, $allow_multiple = false)
	{
		global $CONFIG;

		$entity_guid = (int)$entity_guid;		
        $value_type = detect_extender_valuetype($value, sanitise_string(trim($value_type)));
		$time = time();		
		$owner_guid = (int)$owner_guid;
		$allow_multiple = (boolean)$allow_multiple;
		
		if ($owner_guid==0) 
            $owner_guid = get_loggedin_userid();
		
		$id = false;
	
        $nameId = add_metastring($name);
        if (!$nameId) 
            return false;                
    
		$existing = get_data_row_2("SELECT * from metadata WHERE entity_guid = ? and name_id = ? limit 1",        
            array($entity_guid, $nameId)
        );        

		if (($existing) && (isset($value))) 
		{             
			$id = $existing->id;
			$result = update_metadata($id, $name, $value, $value_type, $owner_guid, $access_id);
			
			if (!$result) return false;
		}
		else if (isset($value))
		{        
			// Support boolean types
			if (is_bool($value)) {
                $value = ($value) ? 1 : 0;
			}
			
			$valueId = add_metastring($value);
			if (!$valueId) 
                return false;			
			
			$id = insert_data_2("INSERT into metadata (entity_guid, name_id, value_id, value_type, owner_guid, time_created, access_id) VALUES (?,?,?,?,?,?,?)", 
                array($entity_guid, $nameId, $valueId, $value_type, $owner_guid, $time, $access_id)
            );			
		} 
        else if ($existing) 
        {
			$id = $existing->id;
			delete_metadata($id);			
		}
		
		return $id;
	}
	
	/**
	 * Update an item of metadata.
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $value
	 * @param string $value_type
	 * @param int $owner_guid
	 * @param int $access_id
	 */
	function update_metadata($id, $name, $value, $value_type, $owner_guid, $access_id)
	{
		$value_type = detect_extender_valuetype($value, sanitise_string(trim($value_type)));
		
		$owner_guid = (int)$owner_guid;
		if ($owner_guid==0) 
            $owner_guid = get_loggedin_userid();
		
		// Support boolean types (as integers)
		if (is_bool($value))
        {
			$value = ($value) ? 1 : 0;
		}
		
		// Add the metastring
		$valueId = add_metastring($value);
        if (!$valueId) 
            return false;
		
		$nameId = add_metastring($name);
        if (!$nameId) 
            return false;

		return update_data_2("UPDATE metadata set value_id=?, value_type=?, access_id=?, owner_guid=? where id=? and name_id=?",
            array($valueId, $value_type, $access_id, $owner-guid, $id, $nameId)
        );

	}   
	
	/**
	 * This function creates metadata from an associative array of "key => value" pairs.
	 * 
	 * @param int $entity_guid
	 * @param string $name_and_values
	 * @param string $value_type
	 * @param int $owner_guid
	 * @param int $access_id
	 * @param bool $allow_multiple
	 */
	function create_metadata_from_array($entity_guid, array $name_and_values, $value_type, $owner_guid, $access_id = ACCESS_PRIVATE, $allow_multiple = false)
	{
		foreach ($name_and_values as $k => $v)
			if (!create_metadata($entity_guid, $k, $v, $value_type, $owner_guid, $access_id, $allow_multiple)) return false;
		
		return true;
	}
	
	/**
	 * Delete an item of metadata, where the current user has access.
	 * 
	 * @param $id int The item of metadata to delete.
	 */
	function delete_metadata($id)
	{
		return delete_data_2("DELETE from metadata where id=?", array((int)$id));
	}
	
	/**
	 * Return the metadata values that match your query.
	 * 
	 * @param string $meta_name
	 * @return mixed ElggMetadata or false.
	 */
	function get_metadata_byname($entity_guid,  $meta_name)
	{
		$nameId = get_metastring_id($meta_name);
		
        if (empty($nameId)) 
            return false;
		
		$access = get_access_sql_suffix("e");
		$md_access = get_access_sql_suffix("m");

        return row_to_elggmetadata(get_data_row_2(
            "SELECT m.*, n.string as name, v.string as value from metadata m JOIN entities e ON e.guid = m.entity_guid JOIN metastrings v on m.value_id = v.id JOIN metastrings n on m.name_id = n.id where m.entity_guid=? and m.name_id=? and $access and $md_access LIMIT 1",
            array((int)$entity_guid, $nameId)
        ));
	}
	
	/**
	 * Return all the metadata for a given GUID.
	 * 
	 * @param int $entity_guid
	 */
	function get_metadata_for_entity($entity_guid)
	{
		global $CONFIG;
	
		$access = get_access_sql_suffix("e");
		$md_access = get_access_sql_suffix("m");
		
        return array_map('row_to_elggmetadata', get_data_2(
            "SELECT m.*, n.string as name, v.string as value from metadata m JOIN entities e ON e.guid = m.entity_guid JOIN metastrings v on m.value_id = v.id JOIN metastrings n on m.name_id = n.id where m.entity_guid=? and $access and $md_access",             
            array((int)$entity_guid)
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
			$where[] = "m.name_id=?";
            $args[] = get_metastring_id($meta_name);
        }    

		if ($meta_value!=="")
        {
			$where[] = "m.value_id=?";
            $args[] = get_metastring_id($meta_value);
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
		$query .= get_access_sql_suffix("e") . ' and ' . get_access_sql_suffix("m"); 
		
		if (!$count) 
        {            
            $order_by = sanitise_string($order_by);
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
            
            return array_map('entity_row_to_elggstar', get_data_2($query, $args));
		} 
        else 
        {
			if ($row = get_data_row_2($query, $args))
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
        return delete_data_2("DELETE from metadata where entity_guid=?", array($entity_guid));
	}
	
	function clear_metadata_by_owner($owner_guid)
	{
        return delete_data_2("DELETE from metadata WHERE owner_guid=?", array($owner_guid));
	}	

	/**
	 * Takes in a comma-separated string and returns an array of tags which have been trimmed and set to lower case
	 *
	 * @param string $string Comma-separated tag string
	 * @return array|false An array of strings, or false on failure
	 */
	function string_to_tag_array($string) {
		
		if (is_string($string)) {
			$ar = explode(",",$string);
			$ar = array_map('trim', $ar); // trim blank spaces
			$ar = array_map('elgg_strtolower', $ar); // make lower case : [Marcus Povey 20090605 - Using mb wrapper function using UTF8 safe function where available]
			$ar = array_filter($ar, 'is_not_null'); // Remove null values
			return $ar;
		}
		return false;
		
	}
	
	/**
	 * Get the URL for this item of metadata, by default this links to the export handler in the current view.
	 *
	 * @param int $id
	 */
	function get_metadata_url($id)
	{
		$id = (int)$id;
		
		if ($extender = get_metadata($id)) {
			return get_extender_url($extender);	
		} 
		return false;
	}
	
	/**
	 * Register a metadata url handler.
	 *
	 * @param string $function_name The function.
	 * @param string $extender_name The name, default 'all'.
	 */
	function register_metadata_url_handler($function_name, $extender_name = "all") {
		return register_extender_url_handler($function_name, 'metadata', $extender_name);
	}
		
	
?>
