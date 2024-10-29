<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service\Handlers;

use App\BankAccounts\Application\Command\Credit;
use App\BankAccounts\Application\Service\Exceptions\IncompatibleCurrencyType;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationType;
use App\BankAccounts\Application\Service\Handlers\CreditOperationHandler;
use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\SharedContext\Currency;
use App\BankAccounts\SharedContext\OperationType;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;

class CreditOperationHandlerTest extends HandlerBaseTestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function shouldCreditHandle(): void
    {
        $operationDate = new DateTime();
        $testId = 'abcd';

        $command = new Credit(
            OperationType::CREDIT,
            new BankAccount($testId, Currency::EUR),
            1000.00,
            Currency::EUR,
            $operationDate
        );

        $expectedEntity = new BankAccountOperation(
            $testId,
            OperationType::CREDIT,
            Currency::EUR,
            1000.00,
            0.0,
            0.0,
            1000.00,
            $operationDate
        );
        $this->repository->expects($this->once())->method('addBankAccountOperation')->with($expectedEntity);
        $this->idProvider->expects($this->once())->method('getId')->willReturn($testId);
        $this->costCalculator->expects($this->once())->method('getCreditOperationCost')->willReturn(0.00);

        $sut = new CreditOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
        );
        $sut->handle($command);
    }

    #[DataProvider('getInvalidCurrencyTypedData')]
    #[Test]
    public function shouldThrowExceptionWhenCreditCurrencyIncompatible(Credit $command): void
    {
        $sut = new CreditOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
        );
        $this->expectException(IncompatibleCurrencyType::class);
        $sut->handle($command);
    }

    #[Test]
    public function shouldThrowExceptionWhenInvalidCreditOperationType(): void
    {
        $command = new Credit(
            OperationType::DEBIT,
            new BankAccount('id-abcd', Currency::PLN),
            1000.00,
            Currency::PLN,
            new DateTime()
        );

        $sut = new CreditOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
        );
        $this->expectException(InvalidOperationType::class);
        $sut->handle($command);
    }

    #[DataProvider('getInvalidCreditAmountData')]
    #[Test]
    public function shouldThrowExceptionWhenInvalidCreditOperationAmount(Credit $command): void
    {
        $sut = new CreditOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
        );
        $this->expectException(InvalidOperationAmount::class);
        $sut->handle($command);
    }

    public static function getInvalidCurrencyTypedData(): array
    {
        $testId = 'id-abcd';
        $operationDate = new DateTime();
        return [
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::PLN),
                    1000.00,
                    Currency::EUR,
                    $operationDate
                ),
            ],
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::EUR),
                    1000.00,
                    Currency::USD,
                    $operationDate
                ),
            ],
        ];
    }

    public static function getInvalidCreditAmountData(): array
    {
        $testId = 'id-abcd';
        $operationDate = new DateTime();
        return [
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::EUR),
                    0.00,
                    Currency::EUR,
                    $operationDate
                ),
            ],
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::EUR),
                    -2.00,
                    Currency::EUR,
                    $operationDate
                ),
            ],
        ];
    }
}
