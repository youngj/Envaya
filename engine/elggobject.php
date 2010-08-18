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
            $this->setDataType(DataType::Image, false);
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

            $this->setDataType(DataType::Image, true);
        }
        $this->save();
    }
    
    public function setContent($content, $isHTML)
    {
        if ($isHTML)
        {
            $content = sanitize_html($content);
        }
        else
        {
            $content = view('output/longtext', array('value' => $content));
        }

        $this->content = $content;
        $this->setDataType(DataType::HTML, true);

        if ($isHTML)
        {
            $thumbnailUrl = get_thumbnail_src($content);

            if ($thumbnailUrl != null)
            {
                $this->setDataType(DataType::Image, $thumbnailUrl != null);
                $this->thumbnail_url = $thumbnailUrl;
            }
        }

        if (!$this->language)
        {
            $this->language = GoogleTranslate::guess_language($this->content);
        }
    }

    public function renderContent()
    {
        $isHTML = $this->hasDataType(DataType::HTML);

        $content = $this->translate_field('content', $isHTML);

        if ($isHTML)
        {
            return $content; // html content should be sanitized when it is input!
        }
        else
        {
            return view('output/longtext', array('value' => $content));
        }
    }

    public function hasDataType($dataType)
    {
        return ($this->data_types & $dataType) != 0;
    }

    public function setDataType($dataType, $val)
    {
        if ($val)
        {
            $this->data_types |= $dataType;
        }
        else
        {
            $this->data_types &= ~$dataType;
        }
    }

    static function query()
    {
        $query = new Query_SelectEntity(static::$table_name);
        $query->where("type='object'");
        $query->where("subtype=?", static::$subtype_id);
        return $query;
    }
    
}
