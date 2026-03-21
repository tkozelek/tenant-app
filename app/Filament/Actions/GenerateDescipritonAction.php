<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GenerateDescipritonAction extends Action
{
    private ?string $referenceField = 'name';

    private ?string $context = 'produkt';

    private string $title = 'name';

    public static function getDefaultName(): ?string
    {
        return 'generate';
    }

    public function references(string $field): static
    {
        $this->referenceField = $field;

        return $this;
    }

    public function context(string $context)
    {
        $this->context = $context;

        return $this;
    }

    public function title(string $tite)
    {
        $this->title = $tite;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-sparkles')
            ->label('Generovať')
            ->action(function (Get $get, Set $set, Component $component) {
                $targetField = $component->getName();
                $title = $this->title ? $get($this->title) : null;

                $currentContent = $this->referenceField
                    ? $get($this->referenceField)
                    : $component->getState();

                if (empty($title)) {
                    Notification::make()
                        ->warning()
                        ->title("Zadajte hodnotu pre pole: {$this->title}")
                        ->send();

                    return;
                }

                try {
                    $prompt = $currentContent
                        ? "Si expert na e-commerce. Tu je návrh popisu pre {$this->context} '{$title}': '{$currentContent}'. Vylepši ho, aby bol profesionálny a pútavý v slovenčine. Vráť VÝHRADNE platný HTML kód."
                        : "Si expert na e-commerce. Napíš pútavý popis pre {$this->context} '{$title}' v slovenčine. Vráť VÝHRADNE platný HTML kód.";

                    if (config('app.debug')) {
                        $prompt .= ' Debug verzia, vloz len 100 znakov max. But use Headings tags etc for testing. And append DEBUG at the end.';
                    }

                    $result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);
                    $generatedHtml = $result->text();

                    $generatedHtml = preg_replace('/```html\n?(.*?)\n?```/s', '$1', $generatedHtml);

                    $set($targetField, trim($generatedHtml));

                    Notification::make()->success()->title('Vygenerované!')->send();
                } catch (\Exception $e) {
                    Notification::make()->danger()->title('Nepodarilo sa pripojiť k AI.')->send();
                    Log::error('Error generating AI text: '.$e->getMessage());
                }
            });
    }
}
