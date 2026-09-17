<?php

namespace App\Services;

use App\Models\Deceased;
use Illuminate\Http\UploadedFile;
use Throwable;

class MortuaryAiService
{
    public function __construct(
        protected HuggingFaceService $huggingface
    ) {
    }

    /**
     * @return array{text:string,provider:string,language:string}
     */
    public function generateFairePart(
        Deceased $deceased,
        string $language = 'fr',
        ?UploadedFile $image = null
    ): array {
        $language = $this->normalizeLanguage($language);
        $prompt = $this->fairePartPrompt($deceased, $language);

        return $this->generate(
            language: $language,
            system: $this->systemPrompt($language, 'faire_part'),
            user: $prompt,
            fallback: fn () => $this->fallbackFairePart($deceased, $language),
            image: $image
        );
    }

    /**
     * @return array{text:string,provider:string,language:string}
     */
    public function generateCondolences(
        Deceased $deceased,
        string $language = 'fr',
        string $tone = 'formal',
        ?UploadedFile $image = null
    ): array {
        $language = $this->normalizeLanguage($language);
        $info = $this->deceasedFacts($deceased, $language);
        $prompt = $language === 'en'
            ? "Write a short {$tone} condolence message for the family of:\n{$info}\nDo not invent details. 80-120 words. No markdown."
            : "Rédige un court message de condoléances ({$tone}) pour la famille de:\n{$info}\nN’invente aucun détail. 80-120 mots. Pas de markdown.";

        return $this->generate(
            language: $language,
            system: $this->systemPrompt($language, 'condolences'),
            user: $prompt,
            fallback: fn () => $this->fallbackCondolences($deceased, $language),
            image: $image
        );
    }

    /**
     * @return array{text:string,provider:string,language:string}
     */
    public function generateFamilySms(
        Deceased $deceased,
        string $purpose = 'pickup',
        string $language = 'fr'
    ): array {
        $language = $this->normalizeLanguage($language);
        $info = $this->deceasedFacts($deceased, $language);
        $purposeLabel = match ($purpose) {
            'payment' => $language === 'en' ? 'payment reminder' : 'rappel de paiement',
            'schedule' => $language === 'en' ? 'ceremony schedule update' : 'mise à jour du planning des cérémonies',
            default => $language === 'en' ? 'body release / pickup notice' : 'avis de levée / récupération',
        };

        $prompt = $language === 'en'
            ? "Write one concise SMS ({$purposeLabel}) about:\n{$info}\nMax 240 characters. No markdown. Do not invent facts."
            : "Rédige un SMS concis ({$purposeLabel}) concernant:\n{$info}\nMaximum 240 caractères. Pas de markdown. N’invente aucun fait.";

        return $this->generate(
            language: $language,
            system: $this->systemPrompt($language, 'sms'),
            user: $prompt,
            fallback: fn () => $this->fallbackSms($deceased, $purpose, $language),
            maxTokens: 180
        );
    }

    /**
     * @return array{text:string,provider:string,language:string}
     */
    public function generateCaseSummary(Deceased $deceased, string $language = 'fr'): array
    {
        $language = $this->normalizeLanguage($language);
        $info = $this->deceasedFacts($deceased, $language);
        $prompt = $language === 'en'
            ? "Summarize this mortuary case for staff in 5 short plain-text lines:\n{$info}\nDo not invent missing data."
            : "Résume ce dossier mortuaire pour le personnel en 5 lignes courtes (texte simple):\n{$info}\nN’invente aucune donnée manquante.";

        return $this->generate(
            language: $language,
            system: $this->systemPrompt($language, 'summary'),
            user: $prompt,
            fallback: fn () => $this->fallbackSummary($deceased, $language)
        );
    }

    public function normalizeLanguage(?string $language): string
    {
        $language = strtolower(trim((string) $language));

        return in_array($language, ['en', 'fr'], true) ? $language : 'fr';
    }

    protected function systemPrompt(string $language, string $feature): string
    {
        if ($language === 'en') {
            return "You are a mortuary management writing assistant.\n"
                . "CRITICAL: Reply ONLY in English. Never mix French into the answer.\n"
                . "Never invent facts. Omit unknown details.\n"
                . "No markdown, no preamble, no explanation about being an AI.\n"
                . "Feature: {$feature}.";
        }

        return "Tu es un assistant de rédaction pour une morgue.\n"
            . "CRITIQUE: Réponds UNIQUEMENT en français. N’utilise jamais l’anglais dans la réponse.\n"
            . "N’invente jamais de faits. Omets les détails inconnus.\n"
            . "Pas de markdown, pas d’introduction, ne dis pas que tu es une IA.\n"
            . "Fonctionnalité: {$feature}.";
    }

    /**
     * @param  callable():string  $fallback
     * @return array{text:string,provider:string,language:string}
     */
    protected function generate(
        string $language,
        string $system,
        string $user,
        callable $fallback,
        ?UploadedFile $image = null,
        int $maxTokens = 900
    ): array {
        if ($this->huggingface->isConfigured()) {
            try {
                $userContent = $this->huggingface->buildUserContent($user, $image);

                $text = $this->huggingface->chat([
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $userContent],
                ], $maxTokens);

                return [
                    'text' => $text,
                    'provider' => 'huggingface',
                    'language' => $language,
                ];
            } catch (Throwable $e) {
                report($e);
            }
        }

        return [
            'text' => $fallback(),
            'provider' => 'local',
            'language' => $language,
        ];
    }

    protected function deceasedFacts(Deceased $deceased, string $language = 'fr'): string
    {
        $labels = $language === 'en'
            ? [
                'full_name' => 'Full name',
                'identifier' => 'Identifier',
                'gender' => 'Gender',
                'date_of_birth' => 'Date of birth',
                'date_of_death' => 'Date of death',
                'admission_date' => 'Admission date',
                'cause_of_death' => 'Cause of death',
                'room_name' => 'Room',
                'room_type' => 'Room type',
                'location_address' => 'Location',
            ]
            : [
                'full_name' => 'Nom complet',
                'identifier' => 'Identifiant',
                'gender' => 'Genre',
                'date_of_birth' => 'Date de naissance',
                'date_of_death' => 'Date de décès',
                'admission_date' => 'Date d’admission',
                'cause_of_death' => 'Cause du décès',
                'room_name' => 'Chambre',
                'room_type' => 'Type de chambre',
                'location_address' => 'Localisation',
            ];

        $lines = [];
        foreach ($labels as $field => $label) {
            $value = $deceased->{$field} ?? null;
            if (filled($value)) {
                $lines[] = "{$label}: {$value}";
            }
        }

        return implode("\n", $lines);
    }

    protected function fairePartPrompt(Deceased $deceased, string $language): string
    {
        $facts = $this->deceasedFacts($deceased, $language);

        if ($language === 'en') {
            return <<<PROMPT
Write the final funeral announcement in English using ONLY these facts:
{$facts}

Rules:
- Output language: English only.
- Never invent relatives, dates, religion, occupation, or burial details.
- Omit missing information.
- Formal compassionate tone.
- No markdown.
- Suitable for printing.
PROMPT;
        }

        return <<<PROMPT
Rédige le faire-part final en français avec UNIQUEMENT ces informations:
{$facts}

Règles:
- Langue de sortie: français uniquement.
- N’invente jamais de proches, dates, religion, métier ou informations d’inhumation.
- Omets ce qui manque.
- Ton formel et compatissant.
- Pas de markdown.
- Adapté à l’impression.
PROMPT;
    }

    protected function fallbackFairePart(Deceased $deceased, string $language): string
    {
        $name = $deceased->full_name;
        $death = $deceased->date_of_death;
        $admission = $deceased->admission_date;
        $identifier = $deceased->identifier;

        if ($language === 'en') {
            $text = "It is with deep sorrow that we announce the passing of {$name}.";
            if ($death) {
                $text .= "\n\nDate of death: {$death}.";
            }
            if ($admission) {
                $text .= "\nAdmitted to our care on {$admission}.";
            }
            if ($identifier) {
                $text .= "\nCase reference: {$identifier}.";
            }
            $text .= "\n\nFamily and friends are invited to join in prayer and remembrance according to arrangements communicated by the family.\n\nMay their soul rest in peace.";

            return $text;
        }

        $text = "C’est avec une profonde tristesse que nous annonçons le rappel à Dieu de {$name}.";
        if ($death) {
            $text .= "\n\nDate du décès : {$death}.";
        }
        if ($admission) {
            $text .= "\nPrise en charge à la morgue le : {$admission}.";
        }
        if ($identifier) {
            $text .= "\nRéférence du dossier : {$identifier}.";
        }
        $text .= "\n\nLa famille et les amis sont invités à s’unir dans la prière et le recueillement selon les dispositions communiquées par la famille.\n\nQue son âme repose en paix.";

        return $text;
    }

    protected function fallbackCondolences(Deceased $deceased, string $language): string
    {
        $name = $deceased->full_name;

        if ($language === 'en') {
            return "Dear family,\n\nPlease accept our sincere condolences on the passing of {$name}. In this time of sorrow, we share in your grief and remain available to support you with dignity and care.\n\nWith deepest sympathy,\nMortuary Management Team";
        }

        return "Chère famille,\n\nNous vous présentons nos sincères condoléances suite au rappel à Dieu de {$name}. En cette épreuve, nous partageons votre peine et restons à votre disposition pour vous accompagner avec dignité et respect.\n\nAvec toute notre compassion,\nL’équipe de la morgue";
    }

    protected function fallbackSms(Deceased $deceased, string $purpose, string $language): string
    {
        $name = $deceased->full_name;
        $ref = $deceased->identifier ? " ({$deceased->identifier})" : '';

        if ($language === 'en') {
            return match ($purpose) {
                'payment' => "Mortuary: reminder regarding payment for {$name}{$ref}. Please contact us. Thank you.",
                'schedule' => "Mortuary: ceremony schedule update for {$name}{$ref}. Please contact the office for details.",
                default => "Mortuary: pickup/release notice for {$name}{$ref}. Please contact us to arrange the next steps.",
            };
        }

        return match ($purpose) {
            'payment' => "Morgue: rappel de paiement concernant {$name}{$ref}. Merci de nous contacter.",
            'schedule' => "Morgue: mise à jour du planning pour {$name}{$ref}. Contactez le secrétariat pour les détails.",
            default => "Morgue: avis de levée/récupération pour {$name}{$ref}. Merci de nous contacter pour la suite.",
        };
    }

    protected function fallbackSummary(Deceased $deceased, string $language): string
    {
        $lines = [];
        $lines[] = ($language === 'en' ? 'Name' : 'Nom') . ': ' . $deceased->full_name;
        if ($deceased->identifier) {
            $lines[] = ($language === 'en' ? 'Reference' : 'Référence') . ': ' . $deceased->identifier;
        }
        if ($deceased->date_of_death) {
            $lines[] = ($language === 'en' ? 'Date of death' : 'Date de décès') . ': ' . $deceased->date_of_death;
        }
        if ($deceased->admission_date) {
            $lines[] = ($language === 'en' ? 'Admission' : 'Admission') . ': ' . $deceased->admission_date;
        }
        if ($deceased->room_type || $deceased->room_name) {
            $room = trim(($deceased->room_type ?? '') . ' ' . ($deceased->room_name ?? ''));
            $lines[] = ($language === 'en' ? 'Room' : 'Chambre') . ': ' . $room;
        }
        $lines[] = ($language === 'en' ? 'Status' : 'Statut') . ': ' . ($language === 'en' ? 'Active case' : 'Dossier actif');

        return implode("\n", $lines);
    }
}
