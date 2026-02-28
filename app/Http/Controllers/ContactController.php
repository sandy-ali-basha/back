<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendContactForm(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $contactData = $request->only(['first_name', 'last_name', 'email', 'message']);

        Mail::to('support@dawaaalhayat.com')->send(new ContactMail($contactData));

        return response()->json('Contact form submitted successfully', Response::HTTP_OK);
    }
}
