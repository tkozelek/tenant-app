<?php

namespace App\Enums;

enum XmlFeedPortal: string
{
    case Heureka = 'heureka';
    case Generic = 'generic';

    public function label(): string
    {
        return match ($this) {
            self::Heureka => 'Heureka',
            self::Generic => 'Genericke',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
