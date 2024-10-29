<?php

declare(strict_types=1);

namespace App\BankAccounts\DomainModel;

use App\BankAccounts\Entity\BankAccountOperationCollection;

interface BalanceCalculator
{
    public function getBalance(BankAccountOperationCollection $accountOperations): float;
}
