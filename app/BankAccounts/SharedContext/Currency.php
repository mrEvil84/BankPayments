<?php

declare(strict_types=1);

namespace App\BankAccounts\SharedContext;

enum Currency
{
    case EUR;
    case USD;
    case PLN;

    public function code(): string
    {
        return match ($this) {
            Currency::EUR => 'EUR',
            Currency::USD => 'USD',
            Currency::PLN => 'PLN',
        };
    }
}
