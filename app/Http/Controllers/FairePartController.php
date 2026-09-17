<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\FuneralNotice;
use App\Services\MortuaryAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FairePartController extends Controller
{
    public function create()
    {
        $deceaseds = Deceased::orderBy('full_name')->get();

        return view('faire-part.create', compact('deceaseds'));
    }

    public function generate(Request $request, MortuaryAiService $ai)
    {
        $request->validate([
            'deceased_id' => ['required', 'integer', 'exists:deceaseds,id'],
            'language' => ['required', 'in:fr,en'],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'save_notice' => ['nullable', 'boolean'],
        ]);

        $deceased = Deceased::findOrFail($request->deceased_id);
        $language = $ai->normalizeLanguage($request->input('language', 'fr'));
        $photo = $request->file('photo');

        $result = $ai->generateFairePart($deceased, $language, $photo);
        $fairePart = $result['text'];
        $provider = $result['provider'];

        $photoUrl = null;
        if ($photo) {
            $path = $photo->store('faire-part', 'public');
            $photoUrl = Storage::disk('public')->url($path);
            $deceased->update(['photo' => $path]);
        } elseif ($deceased->photo) {
            $photoUrl = Storage::disk('public')->url($deceased->photo);
        }

        if ($request->boolean('save_notice')) {
            FuneralNotice::create([
                'deceased_id' => $deceased->id,
                'announcement' => $fairePart,
                'theme' => 'Classic',
                'language' => $language === 'en' ? 'English' : 'French',
            ]);
        }

        return view('faire-part.show', [
            'deceased' => $deceased,
            'fairePart' => $fairePart,
            'provider' => $provider,
            'language' => $language,
            'photoUrl' => $photoUrl,
        ]);
    }
}
