<?php

class Partnership extends Entity
{
    static $table_name = 'partnerships';

    static $table_attributes = array(
        'description' => '',
        'partner_guid' => 0,
        'date_formed' => '',
        'approval' => 0,
        'language' => '',
    );

    public function save()
    {
        if (!$this->language)
        {
            $this->language = GoogleTranslate::guess_language($this->description);
        }

        parent::save();
    }

    function get_partner()
    {
        return get_entity($this->partner_guid);
    }

    function is_self_approved()
    {
        return ($this->approval & 1) != 0;
    }

    function set_self_approved($approved)
    {
        if ($approved)
        {
            $this->approval = $this->approval | 1;
        }
        else
        {
            $this->approval = $this->approval & ~1;
        }
    }

    function is_partner_approved()
    {
        return ($this->approval & 2) != 0;
    }

    function set_partner_approved($approved)
    {
        if ($approved)
        {
            $this->approval = $this->approval | 2;
        }
        else
        {
            $this->approval = $this->approval & ~2;
        }
    }

    function get_approve_url()
    {
        return "{$this->get_container_entity()->get_url()}/confirm_partner";
    }
}
