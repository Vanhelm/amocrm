<?php

namespace App\Service\CRM\Amo;

use App\Entity\Contact;
use App\Entity\Lead;
use App\Exceptions\BadUsageException;
use App\Exceptions\BasicException;
use App\Exceptions\LimitExceededException;
use Carbon\Carbon;
use GuzzleHttp\Client;

class Engine
{
    private const APP_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM4ODRlOTgzZTQ2YzhkMWY2YzdlMDExMjNhN2NlNmY4N2NhYTZiYWNhODVlODZlNWYyYTQ0YzVjMTMxYTc0MTljYzA3NGU0MGJhYjQ5MWJmIn0.eyJhdWQiOiJkYzY4YjEzMy1iNWVjLTQ5ZjEtYTdjZi05ODk0OWFlMDIxOTUiLCJqdGkiOiIzODg0ZTk4M2U0NmM4ZDFmNmM3ZTAxMTIzYTdjZTZmODdjYWE2YmFjYTg1ZTg2ZTVmMmE0NGM1YzEzMWE3NDE5Y2MwNzRlNDBiYWI0OTFiZiIsImlhdCI6MTczMjk3ODk5NSwibmJmIjoxNzMyOTc4OTk1LCJleHAiOjE4NTA5NDcyMDAsInN1YiI6IjExODQ0MDgyIiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMyMDk4MzU0LCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiYWM3ZDc1Y2QtMGMyMy00YTQyLWExNGEtYWJkOTRjNDM1YjA0IiwiYXBpX2RvbWFpbiI6ImFwaS1iLmFtb2NybS5ydSJ9.my4Nc7xpPhu2pd34u7exVqip1NeAyZI8f5VvqwgKnH3GUPZ0ieqWeLBVUFTYZSdO-QTtAWLkz7i57DYNRwoS0PWHP4i0zUVX-kBPTgMSEQtJiN25Xk6nqAxxmKHBqR_hv13qGsBXnrNPbMBp_iYrx8hhCiiBX_YxgSxZZyaKwsBnobBAtDmCiL--CENz2qZe9NkXJ_I7YrWrhJ-MiffaExf_zzSLzrVJ8oShmQ0sV-eveuTA8s8eJLhv6JznLe74n2Meyc9gyvImw7p1oJMNe9uAqUPI40qePEFSrNORhtGq2N4VDDhzjjDkMtzxhFSJSjMFIbXSaw6n6mPz3C-r4A';
    private const APP_URL   = 'https://vanhelmvkcom.amocrm.ru';

    private const APP_LIMIT_LOADED_PAGES = 10000;
    private const APP_LIMIT_ITEM_PAGE    = 250;

    private const APP_METHOD_LEAD    = '/api/v4/leads';
    private const APP_METHOD_CONTACT = '/api/v4/contacts';
    private const APP_METHOD_LINK    = '/api/v4/leads/{entity_id}/link';
    private const APP_METHOD_NOTES   = '/api/v4/{entity_type}/{entity_id}/notes';

    private const APP_HTTP_METHOD_GET   = 'GET';
    private const APP_HTTP_METHOD_POST  = 'POST';

    private const APP_ITEM_TYPE_LEADS    = 'leads';
    private const APP_ITEM_TYPE_CONTACTS = 'contacts';

    private const APP_FIELD_ID_PHONE = 737059;
    private const APP_FIELD_ID_EMAIL = 737061;

    public function listLead(): array
    {
        $leads = $this->_loadItems(self::APP_METHOD_LEAD, self::APP_ITEM_TYPE_LEADS, ['with' => self::APP_ITEM_TYPE_CONTACTS]);

        return array_map(function ($lead): Lead {
            $boundContact = $lead['_embedded']['contacts'][0] ?? null;
            $contact      = null;

            if ($boundContact != null) {
                $contact = new Contact($boundContact['id'], null, null, null);
            }

            return new Lead($lead['id'], $lead['name'], Carbon::createFromTimestamp($lead['created_at'])->format('Y-m-d'), $contact);
        }, $leads);
    }

    public function linkContactToLead(Lead $lead, Contact $contact)
    {
        $method = str_replace('{entity_id}', $lead->id, self::APP_METHOD_LINK);

        $this->_post($method, [], [
            [
                'to_entity_id'   => $contact->id,
                'to_entity_type' => self::APP_ITEM_TYPE_CONTACTS,
            ]
        ]);
    }

    public function addNotesToContact(Contact $contact, string $message)
    {
        $method = str_replace('{entity_id}', $contact->id, self::APP_METHOD_NOTES);
        $method = str_replace('{entity_type}', self::APP_ITEM_TYPE_CONTACTS, $method);

        $this->_post($method, [], [
            [
                'note_type' => 'common',
                'params'    => [
                    'text'    => $message,
                ],
            ],
        ]);
    }

    public function createContact(Contact $contact): Contact
    {
        $response = $this->_post(self::APP_METHOD_CONTACT, [], [
            [
                'name' => $contact->name,
                'custom_fields_values' => [
                    [
                        'field_code' => 'PHONE',
                        'field_id' => self::APP_FIELD_ID_PHONE,
                        'values'   => [
                            [
                                'value' => $contact->phone
                            ]
                        ],
                    ],
                    [
                        'field_code' => 'PHONE',
                        'field_id' => self::APP_FIELD_ID_EMAIL,
                        'values'   => [
                            [
                                'value' => $contact->email
                            ]
                        ],
                    ],
                ]
            ]
        ]);

        $item = $response['_embedded'][self::APP_ITEM_TYPE_CONTACTS][0] ?? null;

        if ($item === null) {
            throw new BasicException('Ошибка при создании контакта');
        }

        return new Contact($item['id'], $contact->name, $contact->phone, $contact->email);
    }

    /**
     * @param Contact $contact
     * @return Contact[]
     * @throws BadUsageException
     */
    public function findContact(Contact $contact): array
    {
        $foundContact = null;

        if ($contact->phone === null && $contact->email === null) {
            throw new BadUsageException('Поиск без email и телефона невозможен');
        }

        if ($contact->phone != null) {
            $foundContact = $this->_findClientByPhone($this->preparePhone($contact->phone));
        }

        if ($contact->email !== null) {
//            $foundContact = array_merge($foundContact, $this->_findClientByEmail($contact->email));
        }

        return $foundContact;
    }

    public function preparePhone(string $phoneNumber): string
    {
        $phone = preg_replace('/\s+|-/','', $phoneNumber);
        return preg_replace('/^(?:\+7|7|8)/', '', $phone);
    }

    private function _findClientByPhone(string $phone): array
    {
        $filter         = ['query' => $phone];
        $loadedContacts = $this->_loadItems(self::APP_METHOD_CONTACT, self::APP_ITEM_TYPE_CONTACTS, $filter);
        return $this->_buildContacts($loadedContacts);
    }


    private function _findClientByEmail(string $email): array
    {
        $filter         = ['query' => $email];
        $loadedContacts = $this->_loadItems(self::APP_METHOD_CONTACT, self::APP_ITEM_TYPE_CONTACTS, $filter);
        return $this->_buildContacts($loadedContacts);
    }

    private function _buildContacts(array $contacts): array
    {
        return array_map(function (array $contact): Contact {
            $phone        = null;
            $email        = null;
            $customFields = $contact['custom_fields_values'] ?? [];

            foreach ($customFields as $customField) {
                $fieldCode = $customField['field_code'] ?? null;
                $value     = $customField['values'][0]['value'] ?? null;

                if ($fieldCode === 'PHONE') {
                    $phone = $value;
                }

                if ($fieldCode === 'EMAIL') {
                    $email = $value;
                }
            }

            return new Contact(
                $contact['id'], $contact['name'], $phone, $email
            );
        }, $contacts);
    }

    private function _loadItems(string $methods, ?string $itemsField = null, $params = [])
    {
        $result = [];
        $page   = 1;
        for ($loop = 0; $loop < self::APP_LIMIT_LOADED_PAGES; $loop++) {
            $response   = $this->_get($methods, array_merge($params, ['page' => $page]));
            $itemsField = $itemsField ?? 'items';
            $items      = $response['_embedded'][$itemsField] ?? [];
            $page++;

            $result = array_merge($result, $items);
            if (count($items) < self::APP_LIMIT_ITEM_PAGE) {
                return $result;
            }
        }

        throw new LimitExceededException('Request limit exceeded');
    }

    private function _get(string $method, array $query = []): array
    {
        return $this->_call(self::APP_HTTP_METHOD_GET, $method, $query);
    }

    private function _post(string $method, array $query = [], array $body = []): array
    {
        return $this->_call(self::APP_HTTP_METHOD_POST, $method, $query, $body);
    }

    private function _call(string $httpMethod, string $method, array $query = [], array $params = []): array
    {
        $url   = self::APP_URL . $method;
        $query = count($query) > 0 ? '?' . http_build_query($query) : '';
        $url   = "{$url}{$query}";

        switch ($httpMethod) {
            case self::APP_HTTP_METHOD_GET:
                $result = $this->_request($url, $httpMethod);
                break;
            case self::APP_HTTP_METHOD_POST:
                $result = $this->_request($url, $httpMethod, $params);
                break;
            default:
                throw new BadUsageException('Unsupported method');
        }

        return $result;
    }

    private function _request($url, $httpMethod, $body = []): array
    {
        $client   = new Client();
        $response = $client->request($httpMethod, $url, [
            'headers' => $this->_getRequestHeaders(),
            'json'    => $body,
        ]);

        $code = $response->getStatusCode();

        if ($code < 200 || $code > 204) {
            throw new BasicException("Code: {$code}, Message: {$response->getBody()}");
        }

        return json_decode($response->getBody(), true) ?? [];
    }

    private function _getRequestHeaders(): array
    {
        return [
            'Accept'        => 'application/json',
            'Content-type'  => 'application/json',
            'Authorization' => 'Bearer ' . self::APP_TOKEN,
        ];
    }
}
