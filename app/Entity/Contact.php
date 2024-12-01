<?php

namespace App\Entity;

class Contact
{
    /** @var ?int */
    public $id;
    /** @var ?string */
    public $name;
    /** @var ?string */
    public $phone;
    /** @var ?string */
    public $email;

    public function __construct(?int $id, ?string $name = null, ?string $phone = null, ?string $email = null)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->phone = $phone;
        $this->email = $email;
    }
}
