<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Handlers;

use App\BankAccounts\Application\Command\AccountOperation;
use App\BankAccounts\Application\Service\Exceptions\IncompatibleCurrencyType;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationType;
use App\SharedContext\OperationType;

abstract class OperationsHandler
{
    abstract public function handle(AccountOperation $command): void;

    protected function assertSameCurrency(AccountOperation $accountOperation): void
    {
        if ($accountOperation->getBankAccount()->getCurrency()->code() !== $accountOperation->getCurrency()->code()) {
            throw IncompatibleCurrencyType::create($accountOperation->getOperationType());
        }
    }

    protected function assertAmount(AccountOperation $command): void
    {
        if ($command->getAmount() === 0.0 || $command->getAmount() < 0) {
            throw InvalidOperationAmount::create();
        }
    }

    /**
     * @throws InvalidOperationType
     */
    protected function assertOperationType(AccountOperation $command, OperationType $operationType): void
    {
        if ($command->getOperationType()->type() !== $operationType->type()) {
            throw InvalidOperationType::create($operationType);
        }
    }
}
