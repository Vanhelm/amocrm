<?php

namespace App\Http\Controllers\Core;

use App\Entity\Contact;
use App\Entity\Lead;
use App\Http\Controllers\Controller;
use App\Service\CRM\ICRM;
use App\Service\Logger\ILogger;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function leadListAction(ICRM $crm)
    {
        return response()->json(array_reverse($crm->getLeads()));
    }

    public function linkContactToLead(Request $request, ICRM $crm, ILogger $logger)
    {
        $request->validate([
            'name'      => 'required|string',
            'phone'     => 'required|string',
            'leadId'    => 'required|integer',
            'comment'   => 'required|string',
        ]);
        try {
            $contact = new Contact(null, $request->name, $request->phone, $request->email);
            $contact = $crm->createContact($contact);
            $crm->linkContactToLead(new Lead($request->leadId, $request->title, $request->date, null), $contact);

            $messageLink = "Контакт #{$contact->id} привязан к сделке {$request->leadId}";

            $crm->addNotesToContact($contact, $messageLink);
            $crm->addNotesToContact($contact, $request->comment);
            $logger->createLog($messageLink, 'Привязка контакта');

            return response()->json(['status' => 'success']);
        } catch (\Throwable $t) {
            $logger->createLog($t->getMessage(), 'Привязка контакта', ILogger::APP_LEVEL_LOG_ERROR);
            return response()->json(['status' => 'error'], 400);
        }
    }
}
