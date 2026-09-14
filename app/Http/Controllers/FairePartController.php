<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FairePartController extends Controller
{
    /**
     * Display the deceased selection and photo upload page.
     */
    public function create()
    {
        $deceaseds = Deceased::orderBy('full_name')->get();

        return view('faire-part.create', compact('deceaseds'));
    }

    /**
     * Generate the funeral faire-part.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'deceased_id' => ['required', 'integer', 'exists:deceaseds,id'],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $deceased = Deceased::findOrFail($request->deceased_id);

        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return back()
                ->withInput()
                ->with('error', 'Gemini API key is not configured.');
        }

        /*
        |--------------------------------------------------------------------------
        | Build deceased information
        |--------------------------------------------------------------------------
        */

        $prompt = <<<PROMPT
You are an assistant for a professional mortuary management system.

Create a respectful, dignified and polished French funeral announcement
(faire-part) for the deceased person described below.

IMPORTANT RULES:

- Use ONLY the information provided below.
- Never invent missing information.
- Never invent relatives.
- Never invent funeral dates.
- Never invent burial information.
- Never invent religious information.
- Never invent an occupation.
- Never invent a cause of death.
- If information is missing, simply omit it.
- Use formal and compassionate French.
- Do not mention that you are an AI.
- Do not use Markdown.
- Do not add explanations before or after the announcement.
- The result must be suitable for a printed funeral announcement.

DECEASED INFORMATION:

Full name: {$deceased->full_name}
PROMPT;

        if (!empty($deceased->gender)) {
            $prompt .= "\nGender: {$deceased->gender}";
        }

        if (!empty($deceased->date_of_birth)) {
            $prompt .= "\nDate of birth: {$deceased->date_of_birth}";
        }

        if (!empty($deceased->date_of_death)) {
            $prompt .= "\nDate of death: {$deceased->date_of_death}";
        }

        if (!empty($deceased->admission_date)) {
            $prompt .= "\nAdmission date: {$deceased->admission_date}";
        }

        if (!empty($deceased->room_name)) {
            $prompt .= "\nMortuary room: {$deceased->room_name}";
        }

        if (!empty($deceased->room_type)) {
            $prompt .= "\nRoom type: {$deceased->room_type}";
        }

        $prompt .= <<<PROMPT

Create the announcement with:

1. A respectful opening.
2. The full name of the deceased.
3. The available relevant dates.
4. A short dignified remembrance.
5. A respectful closing message.

Do not create information that was not provided.

Keep the announcement concise, elegant and suitable for printing.
PROMPT;

        /*
        |--------------------------------------------------------------------------
        | Prepare Gemini request
        |--------------------------------------------------------------------------
        */

        $parts = [
            [
                'text' => $prompt,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Add uploaded photograph if provided
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            $imageData = base64_encode(
                file_get_contents($photo->getRealPath())
            );

            $mimeType = $photo->getMimeType();

            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $imageData,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Send request to Gemini
        |--------------------------------------------------------------------------
        */

        try {

            $response = Http::timeout(120)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent',
                    [
                        'contents' => [
                            [
                                'parts' => $parts,
                            ],
                        ],
                    ]
                );

            if (!$response->successful()) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Gemini could not generate the faire-part. Please try again.'
                    );
            }

            $data = $response->json();

            $fairePart =
                $data['candidates'][0]['content']['parts'][0]['text']
                ?? null;

            if (!$fairePart) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Gemini returned an empty response.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Display generated faire-part
            |--------------------------------------------------------------------------
            */

            return view('faire-part.show', [
                'deceased' => $deceased,
                'fairePart' => trim($fairePart),
                'photo' => $request->hasFile('photo')
                    ? $request->file('photo')
                    : null,
            ]);

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to connect to Gemini. Please check your internet connection.'
                );
        }
    }
}