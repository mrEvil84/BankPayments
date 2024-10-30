<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service;

use App\BankAccounts\Application\Command\Credit;
use App\BankAccounts\Application\Command\Debit;
use App\BankAccounts\Application\Service\Exceptions\IncompatibleCurrencyType;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationType;
use App\BankAccounts\Application\Service\Handlers\CreditOperationHandler;
use App\BankAccounts\Application\Service\Handlers\DebitOperationHandler;

readonly class BankAccountService
{
    public function __construct(
        private CreditOperationHandler $creditHandler,
        private DebitOperationHandler  $debitHandler,
    ) {
    }

    /**
     * @throws IncompatibleCurrencyType|InvalidOperationType|InvalidOperationAmount
     */
    public function credit(Credit $command): void
    {
        $this->creditHandler->handle($command);
    }

    public function debit(Debit $command): void
    {
        $this->debitHandler->handle($command);
    }
}
