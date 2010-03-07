<?php

	/**
	 * Elgg objects
	 * Functions to manage multiple or single objects in an Elgg install
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	/**
	 * ElggObject
	 * Representation of an "object" in the system.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 */
	class ElggObject extends ElggEntity
	{
		/**
		 * Initialise the attributes array. 
		 * This is vital to distinguish between metadata and base parameters.
		 * 
		 * Place your base parameters here.
		 */
		protected function initialise_attributes()
		{
			parent::initialise_attributes();
			
			$this->attributes['type'] = "object";
            
            $this->initializeTableAttributes('objects_entity', array(
                'title' => '',
                'description' => '',
            ));                
		}
				
		/**
		 * Construct a new object entity, optionally from a given id value.
		 *
		 * @param mixed $guid If an int, load that GUID. 
		 * 	If a db row then will attempt to load the rest of the data.
		 * @throws Exception if there was a problem creating the object. 
		 */
		function __construct($guid = null) 
		{			
			$this->initialise_attributes();
			
			if (!empty($guid))
			{			
                if ($guid instanceof stdClass) // either a entity row, or a object table row.
                {					
                    $row = $guid;
                    $entityRow = (property_exists($row, 'type')) ? $row : get_entity_as_row($row->guid);
                    $objectEntityRow = (property_exists($row, 'title')) ? $row : get_object_entity_as_row($row->guid);
                                            
                    if (!$this->loadFromTableRow($entityRow) || !$this->loadFromTableRow($objectEntityRow))
						throw new IOException(sprintf(elgg_echo('IOException:FailedToLoadGUID'), get_class(), $guid->guid)); 
				}
				else if ($guid instanceof ElggObject)
				{					
					 foreach ($guid->attributes as $key => $value) 
					 	$this->attributes[$key] = $value;
				}				
				else if ($guid instanceof ElggEntity)
                {
					throw new InvalidParameterException(elgg_echo('InvalidParameterException:NonElggObject'));
                }    
				else if (is_numeric($guid)) 
                {					
					if (!$this->load($guid)) IOException(sprintf(elgg_echo('IOException:FailedToLoadGUID'), get_class(), $guid));
				}				
				else
                {
					throw new InvalidParameterException(elgg_echo('InvalidParameterException:UnrecognisedValue'));
                }    
			}
		}
		
		/**
		 * Override the load function.
		 * This function will ensure that all data is loaded (were possible), so
		 * if only part of the ElggObject is loaded, it'll load the rest.
		 * 
		 * @param int $guid
		 * @return true|false 
		 */
		protected function load($guid)
		{			
			if (!parent::load($guid)) 
				return false;

			if ($this->attributes['type'] != 'object')
				throw new InvalidClassException(sprintf(elgg_echo('InvalidClassException:NotValidElggStar'), $guid, get_class()));
				
            return $this->loadFromTableRow(get_object_entity_as_row($guid));                
		}
		
		/**
		 * Override the save function.
		 * @return true|false
		 */
		public function save()
		{
			// Save generic stuff
			if (!parent::save())
				return false;
			
            return $this->saveTableAttributes('objects_entity');    
		}

		/**
		 * Set the container for this object.
		 *
		 * @param int $container_guid The ID of the container.
		 * @return bool
		 */
		function setContainer($container_guid)
		{
			$container_guid = (int)$container_guid;
			
			return $this->set('container_guid', $container_guid);
		}
		
		/**
		 * Return the container GUID of this object.
		 *
		 * @return int
		 */
		function getContainer()
		{
			return $this->get('container_guid');
		}		       
        
		/**
		 * As getContainer(), but returns the whole entity.
		 */
		function getContainerEntity()
		{
			return get_entity($this->getContainer());						
		}
		
	}

	/**
	 * Return the object specific details of a object by a row.
	 * 
	 * @param int $guid
	 */
	function get_object_entity_as_row($guid)
	{
		return get_data_row_2("SELECT * from objects_entity where guid=?", array($guid));		
	}
		
?>