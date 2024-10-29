<?php

declare(strict_types=1);

namespace App\BankAccounts\DomainModel;

use App\BankAccounts\Application\Command\AccountOperation;

interface BalanceCalculator
{
    /**
     * @param AccountOperation[] $accountOperations
     */
    public function getBalance(array $accountOperations): float;
}