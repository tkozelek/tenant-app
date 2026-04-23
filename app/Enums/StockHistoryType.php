<?php

namespace App\Enums;

enum StockHistoryType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Adjustment = 'adjustment';
    case Return = 'return';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => 'Nákup',
            self::Sale => 'Predaj',
            self::Adjustment => 'Oprava',
            self::Return => 'Vrátenie',
            self::Transfer => 'Prevod',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Purchase => 'success',
            self::Sale => 'danger',
            self::Adjustment => 'warning',
            self::Return => 'info',
            self::Transfer => 'gray',
        };
    }

    public static function options(): array
    {
        $cases = self::cases();
        $arr = [];
        foreach ($cases as $case) {
            $arr[] = [
                $case->value => $case->label(),
            ];
        }

        return $arr;
    }
}
