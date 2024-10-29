<?php

declare(strict_types=1);

namespace App\Payments\Entity;

readonly class Payment
{
    public function __construct(
        private int $paymentId,
        private int $bankAccountId,
        private float $amount,
    ) {
    }

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }

    public function getBankAccountId(): int
    {
        return $this->bankAccountId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
