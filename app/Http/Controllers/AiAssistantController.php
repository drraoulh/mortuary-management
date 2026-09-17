<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\FuneralNotice;
use App\Services\MortuaryAiService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function index()
    {
        $deceaseds = Deceased::orderBy('full_name')->get();
        $notices = FuneralNotice::with('deceased')->latest()->take(10)->get();
        $hfConfigured = filled(config('services.huggingface.token'));

        return view('ai.index', compact('deceaseds', 'notices', 'hfConfigured'));
    }

    public function generate(Request $request, MortuaryAiService $ai)
    {
        $request->validate([
            'deceased_id' => ['required', 'integer', 'exists:deceaseds,id'],
            'feature' => ['required', 'in:faire_part,condolences,sms,summary'],
            'language' => ['nullable', 'in:fr,en'],
            'tone' => ['nullable', 'in:formal,warm'],
            'sms_purpose' => ['nullable', 'in:pickup,payment,schedule'],
            'save_notice' => ['nullable', 'boolean'],
        ]);

        $deceased = Deceased::findOrFail($request->deceased_id);
        $language = $request->input('language', 'fr');

        $result = match ($request->feature) {
            'condolences' => $ai->generateCondolences(
                $deceased,
                $language,
                $request->input('tone', 'formal')
            ),
            'sms' => $ai->generateFamilySms(
                $deceased,
                $request->input('sms_purpose', 'pickup'),
                $language
            ),
            'summary' => $ai->generateCaseSummary($deceased, $language),
            default => $ai->generateFairePart($deceased, $language),
        };

        if ($request->boolean('save_notice') && in_array($request->feature, ['faire_part', 'condolences'], true)) {
            FuneralNotice::create([
                'deceased_id' => $deceased->id,
                'announcement' => $result['text'],
                'theme' => $request->feature === 'condolences' ? 'Condolences' : 'Classic',
                'language' => $language === 'en' ? 'English' : 'French',
            ]);
        }

        $titles = [
            'faire_part' => $language === 'en' ? 'Funeral announcement' : 'Faire-part',
            'condolences' => $language === 'en' ? 'Condolence message' : 'Message de condoléances',
            'sms' => 'SMS',
            'summary' => $language === 'en' ? 'Case summary' : 'Résumé du dossier',
        ];

        return view('ai.result', [
            'deceased' => $deceased,
            'feature' => $request->feature,
            'title' => $titles[$request->feature],
            'text' => $result['text'],
            'provider' => $result['provider'],
            'language' => $language,
        ]);
    }
}
