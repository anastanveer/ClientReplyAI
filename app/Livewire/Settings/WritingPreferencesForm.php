<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class WritingPreferencesForm extends Component
{
    public string $profession = '';

    public string $preferredTone = '';

    public string $defaultUseCase = '';

    public string $defaultLanguage = '';

    public string $signature = '';

    public string $extraInstruction = '';

    public bool $saved = false;

    public function mount(): void
    {
        $profile = auth()->user()->profile;

        if ($profile) {
            $this->profession = $profile->profession ?? '';
            $this->preferredTone = $profile->preferred_tone ?? '';
            $this->defaultUseCase = $profile->default_use_case ?? '';
            $this->defaultLanguage = $profile->default_language ?? '';
            $this->signature = $profile->signature ?? '';
            $this->extraInstruction = $profile->writing_preferences['instruction'] ?? '';
        }
    }

    public function save(): void
    {
        $this->saved = false;

        $this->validate([
            'profession' => ['nullable', 'string', 'max:100'],
            'preferredTone' => ['nullable', 'string', 'max:50'],
            'defaultUseCase' => ['nullable', 'string', 'max:100'],
            'defaultLanguage' => ['nullable', 'string', 'max:100'],
            'signature' => ['nullable', 'string', 'max:500'],
            'extraInstruction' => ['nullable', 'string', 'max:500'],
        ]);

        auth()->user()->profile()->updateOrCreate(
            [],
            [
                'profession' => $this->profession ?: null,
                'preferred_tone' => $this->preferredTone ?: null,
                'default_use_case' => $this->defaultUseCase ?: null,
                'default_language' => $this->defaultLanguage ?: null,
                'signature' => $this->signature ?: null,
                'writing_preferences' => $this->extraInstruction !== ''
                    ? ['instruction' => $this->extraInstruction]
                    : null,
            ]
        );

        $this->saved = true;
    }

    public function render(): View
    {
        return view('livewire.settings.writing-preferences-form', [
            'toneOptions' => $this->toneOptions(),
            'useCaseOptions' => $this->useCaseOptions(),
            'languageOptions' => $this->languageOptions(),
        ]);
    }

    /**
     * @return list<string>
     */
    protected function toneOptions(): array
    {
        return ['Professional', 'Friendly', 'Polite', 'Confident', 'Strong', 'Soft', 'Direct', 'Respectful', 'Short', 'Detailed'];
    }

    /**
     * @return list<string>
     */
    protected function useCaseOptions(): array
    {
        return [
            'Fiverr Client Reply',
            'WhatsApp Business Reply',
            'Email Reply',
            'Job Recruiter Reply',
            'LinkedIn Reply',
            'Proposal Reply',
            'Complaint Reply',
            'Apology Reply',
            'Follow-up Reply',
            'Negotiation Reply',
            'Project Update Reply',
            'Payment Reminder',
            'Delay Update',
            'Asking for Requirements',
            'Asking for Access',
            'Review Request',
            'Support Response',
            'Friendly Daily Reply',
            'Translation Improvement',
            'General Reply',
        ];
    }

    /**
     * @return list<string>
     */
    protected function languageOptions(): array
    {
        return [
            'English Improvement',
            'Broken English to Professional English',
            'Roman Urdu to Professional English',
            'Urdu to English',
            'Hindi to English',
            'English to German',
            'English to French',
            'English to Arabic',
        ];
    }
}
