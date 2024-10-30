<?php

declare(strict_types=1);

namespace App\BankAccounts\Entity;

use ArrayObject;
use InvalidArgumentException;

class BankAccountOperationCollection extends ArrayObject
{
    public function add(BankAccountOperation $operation): void
    {
        $this->offsetSet($operation->getOperationId(), $operation);
    }

    public function offsetSet($key, $value): void
    {
        if (!$value instanceof BankAccountOperation) {
            throw new InvalidArgumentException('Must be an instance of BankAccountOperation');
        }

        parent::offsetSet($key, $value);
    }
}
