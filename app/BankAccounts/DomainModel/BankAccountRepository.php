<?php

declare(strict_types=1);

namespace App\BankAccounts\DomainModel;

use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\Entity\BankAccountOperationCollection;
use DateTime;

interface BankAccountRepository
{
    public function addBankAccountOperation(BankAccountOperation $bankAccountOperation): void;

    public function getDebitOperationsCountAtDate(DateTime $date): int;

    public function getBankAccountOperations(BankAccount $account): BankAccountOperationCollection;
}
