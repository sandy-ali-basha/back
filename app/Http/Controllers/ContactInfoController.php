<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContactInfoService; // السيرفس الجديد الخاص بالـ contact
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Http\Data\AddContactData;
use App\Http\Data\UpdateContactData;

class ContactInfoController extends Controller
{
    public ContactInfoService $contactService;

    public function __construct(ContactInfoService $contactService)
    {
        $this->ContactInfoService = $contactService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = $this->ContactInfoService->getAllContacts();

        $contactResource =  ContactResource::collection($contacts);

        $response = new SuccessResponse($contactResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $data = AddContactData::from($request);
        $contact = $this->ContactInfoService->createContact($data);

        $contact->withTranslation = true;

        $contactResource = new ContactResource($contact);
        $response = new SuccessResponse($contactResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $contact = $this->ContactInfoService->getById($id);

        if (!$contact) {
            $response = new ErrorResponse('Contact not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }


        $contactResource = new ContactResource($contact);
        $response = new SuccessResponse($contactResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $data = UpdateContactData::from($request);
        $contact = $this->ContactInfoService->updateContact($id, $data);

        $contact->withTranslation = true;

        $contactResource = new ContactResource($contact);
        $response = new SuccessResponse($contactResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $this->ContactInfoService->deleteContact($id);

        $response = new SuccessResponse("Contact is deleted", Response::HTTP_OK);
        return response()->success($response);
    }
}
