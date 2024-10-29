<?php

declare(strict_types=1);

namespace App\SharedContext;

enum OperationType
{
    case DEBIT;
    case CREDIT;

    public function type(): string
    {
        return match ($this) {
            OperationType::DEBIT => 'DEBIT',
            OperationType::CREDIT => 'CREDIT',
        };
    }
}
