<?php

	class ElggObject extends ElggEntity
	{
        static $table_name = 'objects_entity';
        static $table_attributes = array(
            'title' => '',
            'description' => '',
        );        
        static $subtype_id = 0;
    
		protected function initialise_attributes()
		{
			parent::initialise_attributes();			
			$this->attributes['type'] = "object";
            
            $this->attributes['subtype'] = static::$subtype_id;                        
            $this->initializeTableAttributes(static::$table_name, static::$table_attributes);                
		}
				
        protected function loadFromPartialTableRow($row)
        {   
            if (parent::loadFromPartialTableRow($row))
            {
                if (!property_exists($row, get_first_key(static::$table_attributes)))
                {
                    $objectEntityRow = $this->selectTableAttributes(static::$table_name, $row->guid);
                    return $this->loadFromTableRow($objectEntityRow);
                }
                return true;
            }
            return false;
            
        }

		protected function load($guid)
		{			
            return parent::load($guid) && $this->loadFromTableRow($this->selectTableAttributes(static::$table_name, $guid));                
		}
        
		public function save()
		{
            return parent::save() && $this->saveTableAttributes(static::$table_name);    
		}
        
        public function delete()
        {
            return parent::delete() && $this->deleteTableAttributes(static::$table_name);
        }
        
        public function setImages($imageFiles)
        {
            if (!$imageFiles)
            {
                $this->data_types &= ~DataType::Image;     
            }
            else
            {   
                foreach ($imageFiles as $size => $srcFile)
                {
                    $srcFile = $imageFiles[$size]['file'];                    

                    $destFile = $this->getImageFile($size);

                    $srcFile->copyTo($destFile);
                    $srcFile->delete();
                }

                $this->data_types |= DataType::Image;     
            }   
            $this->save();
        }        
        
        static function getByCondition($where, $args)
        {
            $objs = static::filterByCondition($where, $args, '', 1, 0, false);
            if (!empty($objs))
            {
                return $objs[0];
            }
            return null;
        }                      
        
        static function filterByCondition($where, $args, $order_by = '', $limit = 10, $offset = 0, $count = false)
        {
            $where[] = "type='object'";

            $subtypeId = static::$subtype_id;
            if ($subtypeId)
            {
                $where[] = "subtype=?";
                $args[] = $subtypeId;
            }                      
            
            return get_entities_by_condition(static::$table_name, $where, $args, $order_by, $limit, $offset, $count);
        }        
	}


    function get_first_key($arr)
    {
        reset($arr);
        $pair = each($arr);
        $res = $pair[0];
        reset($arr);
        return $res;
    }           
?>