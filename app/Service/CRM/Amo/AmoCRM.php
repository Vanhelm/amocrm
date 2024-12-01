<?php

namespace App\Service\CRM\Amo;

use App\Entity\Contact;
use App\Entity\Lead;
use App\Exceptions\BasicException;
use App\Service\CRM\ICRM;
use App\Service\Logger\FileLogService;
use App\Service\Logger\ILogger;

class AmoCRM implements ICRM
{
    /** @var Engine */
    private $_engine;

    /** @var ILogger */
    private $_logger;

    public function __construct(ILogger $logger)
    {
        $this->_engine = new Engine();
        $this->_logger = $logger;
    }

    public function getLeads(): array
    {
        try {
            $leads = $this->_engine->listLead();
            $this->_logger->createLog('load lead succeed', 'Загрузка лидов');
            return $leads;
        } catch (\Throwable $t) {
            $this->_logger->createLog($t->getMessage(), 'Загрузка лидов', ILogger::APP_LEVEL_LOG_ERROR);
            return [];
        }
    }

    public function createContact(Contact $contact): Contact
    {
        try {
            $foundContact = $this->findContact($contact);

            if ($foundContact !== null) {
                return $foundContact;
            }

            $contact = $this->_engine->createContact($contact);
            $this->_logger->createLog('Successfully create contact', 'Создание контакта');
            return $contact;
        } catch (\Throwable $t) {
            $this->_logger->createLog($t->getMessage(), 'Создание контакта', ILogger::APP_LEVEL_LOG_ERROR);
            throw new BasicException();
        }

    }

    public function findContact(Contact $contact): ?Contact {
        $foundContacts = $this->_engine->findContact($contact);

        foreach ($foundContacts as $foundContact) {
            if ($this->_engine->preparePhone($foundContact->phone) === $this->_engine->preparePhone($contact->phone)) {
                $this->_logger->createLog('Contact is founded', 'Поиск контакта');
                return $foundContact;
            }
        }

        return null;
    }

    public function linkContactToLead(Lead $lead, Contact $contact): void
    {
        $this->_engine->linkContactToLead($lead, $contact);
    }

    public function addNotesToContact(Contact $contact, string $message): void
    {
        $this->_engine->addNotesToContact($contact, $message);
    }
}
