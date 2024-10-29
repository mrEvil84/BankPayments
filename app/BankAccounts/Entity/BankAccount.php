<?php

declare(strict_types=1);

namespace App\BankAccounts\Entity;

use App\SharedContext\Currency;

readonly class BankAccount
{
    public function __construct(
        private string   $bankAccountId,
        private Currency $currency
    ) {
    }

    public function getBankAccountId(): string
    {
        return $this->bankAccountId;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}