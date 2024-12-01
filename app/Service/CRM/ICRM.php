<?php

namespace App\Service\CRM;

use App\Entity\Contact;
use App\Entity\Lead;

interface ICRM
{
    public function getLeads(): array;
    public function findContact(Contact $contact): ?Contact;
    public function createContact(Contact $contact): ?Contact;
    public function linkContactToLead(Lead $lead, Contact $contact): void;
    public function addNotesToContact(Contact $contact, string $message): void;
}
