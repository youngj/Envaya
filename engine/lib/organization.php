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

    public function getSource()
    {
        return "<a href='http://translate.google.com'>Google Translate</a>";
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