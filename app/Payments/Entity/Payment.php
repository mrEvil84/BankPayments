<?php

declare(strict_types=1);

namespace App\Payments\Entity;

use App\SharedContext\Currency;

readonly class Payment
{
    public function __construct(
        private ?string $paymentId,
        private float $amount,
        private Currency $currency,
    ) {
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
