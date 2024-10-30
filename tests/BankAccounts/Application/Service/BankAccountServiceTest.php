<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service;

use App\BankAccounts\Application\Command\Credit;
use App\BankAccounts\Application\Command\Debit;
use App\BankAccounts\Application\Service\BankAccountService;
use App\BankAccounts\Application\Service\Handlers\CreditOperationHandler;
use App\BankAccounts\Application\Service\Handlers\DebitOperationHandler;
use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\SharedContext\Currency;
use App\BankAccounts\SharedContext\OperationType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BankAccountServiceTest extends TestCase
{
    private CreditOperationHandler|MockObject $creditOperationHandler;
    private DebitOperationHandler|MockObject $debitOperationHandler;
    protected function setUp(): void
    {
        parent::setUp();
        $this->creditOperationHandler = $this->createMock(CreditOperationHandler::class);
        $this->debitOperationHandler = $this->createMock(DebitOperationHandler::class);
    }

    #[Test]
    public function shouldCreditTrivialTest(): void
    {
        $command = new Credit(
            OperationType::CREDIT,
            new BankAccount('test-id', Currency::EUR),
            100.00,
            Currency::EUR,
            new \DateTime()
        );

        $this->creditOperationHandler->expects($this->once())->method('handle')->with($command);

        $sut = new BankAccountService($this->creditOperationHandler, $this->debitOperationHandler);
        $sut->credit($command);
    }


    #[Test]
    public function shouldDebitTrivialTest(): void
    {
        $command = new Debit(
            OperationType::DEBIT,
            new BankAccount('test-id', Currency::EUR),
            100.00,
            Currency::EUR,
            new \DateTime()
        );

        $this->debitOperationHandler->expects($this->once())->method('handle')->with($command);

        $sut = new BankAccountService($this->creditOperationHandler, $this->debitOperationHandler);
        $sut->debit($command);
    }
}
