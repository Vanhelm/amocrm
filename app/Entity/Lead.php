<?php

namespace App\Entity;

class Lead
{
    /** @var int */
    public $id;
    /** @var ?string */
    public $title;
    /** @var  ?string */
    public $date;
    /** @var ?Contact */
    public $contact;

    public function __construct($id, $title, $date, $contact)
    {
        $this->id      = $id;
        $this->title   = $title;
        $this->date    = $date;
        $this->contact = $contact;
    }
}
