<?php

class UploadedFile extends Entity
{
    static $subtype_id = T_file;

    static $table_name = 'files_entity';
    static $table_attributes = array(
        'group_name' => '',
        'filename' => '',
        'size' => '',
        'width' => null,
        'height' => null,
        'mime' => '',
    );

    public function get_files_in_group()
    {
        return UploadedFile::query()->where('owner_guid = ?', $this->owner_guid)->
            where('group_name = ?', $this->group_name)->filter();            
    }

    public function js_properties()
    {
        return array(
            'guid' => $this->guid,
            'size' => $this->size,
            'group_name' => $this->group_name,
            'filename' => $this->filename,
            'mime' => $this->mime,
            'width' => $this->width,
            'height' => $this->height,
            'url' => $this->get_url(),
        );
    }

    public function get_path()
    {
        if ($this->group_name)
        {
            return "{$this->owner_guid}/{$this->group_name}/{$this->filename}";
        }
        else
        {
            return "{$this->owner_guid}/{$this->filename}";
        }
    }

    public function get_url()
    {
        global $CONFIG;
        return "http://{$CONFIG->s3_bucket}.s3.amazonaws.com/{$this->get_path()}";
    }

    public function delete()
    {
        global $CONFIG;
        $res = get_s3()->deleteObject($CONFIG->s3_bucket, $this->get_path());

        if ($res && $this->guid)
        {
            return parent::delete();
        }
        else
        {
            return $res;
        }
    }

    public function size()
    {
        global $CONFIG;
        $info = get_s3()->getObjectInfo($CONFIG->s3_bucket, $this->get_path());
        if ($info)
        {
            return $info['Content-Length'];
        }
        return -1;
    }

    public function upload_file($filePath, $mime = null)
    {
        global $CONFIG;

        $headers = array();
        if ($mime)
        {
            $headers['Content-Type'] = $mime;
        }

        return get_s3()->uploadFile($CONFIG->s3_bucket, $this->get_path(), $filePath, true, $headers);
    }

    public function copy_to($destFile)
    {
        global $CONFIG;
        $res = get_s3()->copyObject($CONFIG->s3_bucket, $this->get_path(), $CONFIG->s3_bucket, $destFile->get_path(), true);
        return $res;
    }

    public function exists()
    {
        global $CONFIG;
        $info = get_s3()->getObjectInfo($CONFIG->s3_bucket, $this->get_path());
        return ($info) ? true : false;
    }
}
