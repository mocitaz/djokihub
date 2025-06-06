<?php

// File: app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        // return view('clients.index', compact('clients')); // Anda perlu buat view ini
        return response()->json($clients); // Contoh response JSON untuk API
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_name' => 'required|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255|unique:clients,contact_email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $validatedData['created_by_user_id'] = auth()->id(); // Jika ingin mencatat siapa yang membuat

        $client = Client::create($validatedData);
        // return redirect()->route('clients.index')->with('success', 'Client berhasil ditambahkan.');
        return response()->json($client, 201); // Contoh response JSON
    }

    // Implementasikan method show, edit, update, destroy lainnya
}