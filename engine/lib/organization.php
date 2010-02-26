<?php 

require_once("users.php");

// Class source
class Organization extends ElggUser {

    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = 'organization';

        //Notes:
        // this->isVerifyingOrg
        // verifiedBy... relationship
        // when admin performs verification, he/she chooses from a dropdown of the Orgs he/she is an admin of.  Organization is then verified by this chosen super org
        // entity_relationships
        //addRelationship(...



        /*
        TODO: implement or integrate Enum implementation and use it instead of constants.
        approval values:
        -1 -- Denied approval
        0 -- Awaiting approval
        1 -- Approved
        2 -- Approved by super-admin
        3 -- Verified by partner organization
        4 -- Verified by Envaya host country national
        5 -- Verified by super-admin
        */
    }

    public function __construct($guid = null) 
    {
        parent::__construct($guid);
    }

    public function set($name, $value)
    {
        if ($name == 'name' || $name == 'username')
        {
            $this->setMetaData($name, $value);
        }
        
        return parent::set($name, $value);
    }

    public function isApproved()
    {
        return $this->approval > 0;
    }

    public function isVerified()
    {
        return $this->approval > 2;
    }

    public function generateEmailCode()
    {
        $code = '';
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        for ($p = 0; $p < 8; $p++)
        {
            $code .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $this->email_code = $code;
    }
    
    public function getIconFile($size)
    {
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->guid;
        $filehandler->setFilename("icon$size.jpg");
        return $filehandler;
    }   

    public function getPostEmail()
    {
        if (!$this->email_code)
        {
            $this->generateEmailCode();
        }
        return "post+{$this->email_code}@envaya.org";
    }

    public function userCanSee()
    {
        return ($this->isApproved() || isadminloggedin() || ($this->guid == get_loggedin_userid()));
    }

    public function approveOrg()
    {
        if (isadminloggedin())
        {
            $this->set("approval", 2);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function verifyOrg()
    {
        if (isadminloggedin())
        {
            $this->set("approval", 5);
            return true;
        }
        else
        {
            return false;
        }
    }
}

class Translation extends ElggObject
{
    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = 'translation';
    }    
    
    public function getSubtype() 
    {
        return 'translation';            
    }
    

    public function __construct($guid = null) 
    {
        parent::__construct($guid);
    }      
}

class NewsUpdate extends ElggObject
{
    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = 'blog';                
    }
    
    public function __construct($guid = null) 
    {
        parent::__construct($guid);
        $this->access_id = 2;
    }
    
    public function getImageFile($size = '')
    {
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->container_guid;
        $filehandler->setFilename("blog/{$this->guid}$size.jpg");
        return $filehandler;       
    }
    
    public function setImage($imageData)
    {
        $prefix = "blog/".$this->guid;

        $filehandler = new ElggFile();
        
        $filehandler->owner_guid = $this->container_guid;
        $filehandler->container_guid = $this->guid;
        
        $filehandler->setFilename($prefix . ".jpg");
        $filehandler->open("write");
        $filehandler->write($imageData);
        $filehandler->close();

        $thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),100,100, false);
        $thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),450,450, false);

        if ($thumbsmall) 
        {
            $thumb = new ElggFile();
            $thumb->owner_guid = $blog->container_guid;
            $thumb->container_guid = $blog->guid;
            $thumb->setMimeType('image/jpeg');

            $thumb->setFilename($prefix."small.jpg");
            $thumb->open("write");
            $thumb->write($thumbsmall);
            $thumb->close();

            $thumb->setFilename($prefix."large.jpg");
            $thumb->open("write");
            $thumb->write($thumblarge);
            $thumb->close();
        }        
    }
}