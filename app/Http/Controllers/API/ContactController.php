<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => ' Your Message has been sent successfully to shopee.',
            'data' => $contact
        ], 201);
    }


    public function index(): JsonResponse
    {
        $contacts = Contact::latest()->get();

        return response()->json($contacts);
    }


    public function destroy($id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully.',
        ]);
    }
}
