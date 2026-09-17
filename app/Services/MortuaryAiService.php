<?php

namespace App\Services;

use App\Models\Deceased;
use Throwable;

class MortuaryAiService
{
    public function __construct(
        protected HuggingFaceService $huggingface
    ) {
    }

    /**
     * @return array{text:string,provider:string}
     */
    public function generateFairePart(Deceased $deceased, string $language = 'fr'): array
    {
        $prompt = $this->fairePartPrompt($deceased, $language);

        return $this->generate(
            system: $language === 'en'
                ? 'You write dignified funeral announcements. Never invent facts.'
                : 'Tu rédiges des faire-part funéraires dignes et formels. N’invente jamais de faits.',
            user: $prompt,
            fallback: fn () => $this->fallbackFairePart($deceased, $language)
        );
    }

    /**
     * @return array{text:string,provider:string}
     */
    public function generateCondolences(Deceased $deceased, string $language = 'fr', string $tone = 'formal'): array
    {
        $info = $this->deceasedFacts($deceased);
        $prompt = $language === 'en'
            ? "Write a short {$tone} condolence message for the family of:\n{$info}\nDo not invent details. 80-120 words. No markdown."
            : "Rédige un court message de condoléances ({$tone}) pour la famille de:\n{$info}\nN’invente aucun détail. 80-120 mots. Pas de markdown.";

        return $this->generate(
            system: $language === 'en'
                ? 'You write compassionate condolence messages.'
                : 'Tu rédiges des messages de condoléances compatissants.',
            user: $prompt,
            fallback: fn () => $this->fallbackCondolences($deceased, $language)
        );
    }

    /**
     * @return array{text:string,provider:string}
     */
    public function generateFamilySms(Deceased $deceased, string $purpose = 'pickup', string $language = 'fr'): array
    {
        $info = $this->deceasedFacts($deceased);
        $purposeLabel = match ($purpose) {
            'payment' => $language === 'en' ? 'payment reminder' : 'rappel de paiement',
            'schedule' => $language === 'en' ? 'ceremony schedule update' : 'mise à jour du planning des cérémonies',
            default => $language === 'en' ? 'body release / pickup notice' : 'avis de levée / récupération',
        };

        $prompt = $language === 'en'
            ? "Write one concise SMS ({$purposeLabel}) about:\n{$info}\nMax 240 characters. No markdown. Do not invent facts."
            : "Rédige un SMS concis ({$purposeLabel}) concernant:\n{$info}\nMaximum 240 caractères. Pas de markdown. N’invente aucun fait.";

        return $this->generate(
            system: $language === 'en'
                ? 'You write short professional mortuary SMS messages.'
                : 'Tu rédiges des SMS professionnels courts pour une morgue.',
            user: $prompt,
            fallback: fn () => $this->fallbackSms($deceased, $purpose, $language),
            maxTokens: 180
        );
    }

    /**
     * @return array{text:string,provider:string}
     */
    public function generateCaseSummary(Deceased $deceased, string $language = 'fr'): array
    {
        $info = $this->deceasedFacts($deceased);
        $prompt = $language === 'en'
            ? "Summarize this mortuary case for staff in 5 short bullet-like lines (plain text):\n{$info}\nDo not invent missing data."
            : "Résume ce dossier mortuaire pour le personnel en 5 lignes courtes (texte simple):\n{$info}\nN’invente aucune donnée manquante.";

        return $this->generate(
            system: $language === 'en'
                ? 'You summarize mortuary records for staff.'
                : 'Tu résumes des dossiers mortuaires pour le personnel.',
            user: $prompt,
            fallback: fn () => $this->fallbackSummary($deceased, $language)
        );
    }

    /**
     * @param  callable():string  $fallback
     * @return array{text:string,provider:string}
     */
    protected function generate(
        string $system,
        string $user,
        callable $fallback,
        int $maxTokens = 900
    ): array {
        if ($this->huggingface->isConfigured()) {
            try {
                $text = $this->huggingface->chat([
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ], $maxTokens);

                return [
                    'text' => $text,
                    'provider' => 'huggingface',
                ];
            } catch (Throwable $e) {
                report($e);
            }
        }

        return [
            'text' => $fallback(),
            'provider' => 'local',
        ];
    }

    protected function deceasedFacts(Deceased $deceased): string
    {
        $lines = [
            'Full name: ' . $deceased->full_name,
        ];

        foreach ([
            'identifier' => 'Identifier',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of birth',
            'date_of_death' => 'Date of death',
            'admission_date' => 'Admission date',
            'cause_of_death' => 'Cause of death',
            'room_name' => 'Room',
            'room_type' => 'Room type',
            'location_address' => 'Location',
        ] as $field => $label) {
            $value = $deceased->{$field} ?? null;
            if (filled($value)) {
                $lines[] = "{$label}: {$value}";
            }
        }

        return implode("\n", $lines);
    }

    protected function fairePartPrompt(Deceased $deceased, string $language): string
    {
        $facts = $this->deceasedFacts($deceased);

        if ($language === 'en') {
            return <<<PROMPT
Create a respectful funeral announcement using ONLY these facts:
{$facts}

Rules:
- Never invent relatives, dates, religion, occupation, or burial details.
- Omit missing information.
- Formal compassionate English.
- No markdown.
- Suitable for printing.
PROMPT;
        }

        return <<<PROMPT
Crée un faire-part funéraire respectueux en français avec UNIQUEMENT ces informations:
{$facts}

Règles:
- N’invente jamais de proches, dates, religion, métier ou informations d’inhumation.
- Omets ce qui manque.
- Français formel et compatissant.
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
