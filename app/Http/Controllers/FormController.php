<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    private $storagePath = 'storage/app/forms';

    public function showForm()
    {
        return view('form');
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $filename = $this->storagePath.'/submission_'.time().'.json';
        \Storage::makeDirectory($this->storagePath);
        \Storage::put($filename, json_encode($validated, JSON_PRETTY_PRINT));

        return redirect()->route('form.show')->with('success', 'Данные успешно сохранены!');
    }

    public function listSubmissions()
    {
        $files = \Storage::files($this->storagePath);
        $submissions = [];

        foreach ($files as $file) {
            $submissions[] = json_decode(\Storage::get($file), true);
        }

        return view('submissions', ['submissions' => $submissions]);
    }
}
