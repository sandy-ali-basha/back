<?php 

namespace App\Services;

use App\Models\Contact;
use App\Http\Data\AddContactData;
use App\Http\Data\UpdateContactData;
use Illuminate\Database\Eloquent\Collection;

class ContactInfoService
{
    public function getAllContacts() : Collection
    {

        return Contact::all();
        
    }

    public function getById($id)
    {
        return Contact::find($id);
    }

    public function createContact(AddContactData $data)
    {
        return Contact::create((array)$data);
    }

    public function updateContact($id, UpdateContactData $data)
    {
        $contact = Contact::findOrFail($id);
        $contact->update((array)$data);
        return $contact;
    }

    public function deleteContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
    }
}
