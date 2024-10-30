<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service\Handlers;

use App\BankAccounts\Application\Command\Debit;
use App\BankAccounts\Application\Service\Exceptions\InsufficientFunds;
use App\BankAccounts\Application\Service\Exceptions\OperationsLimit;
use App\BankAccounts\Application\Service\Handlers\DebitOperationHandler;
use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\Entity\BankAccountOperationCollection;
use App\SharedContext\Currency;
use App\SharedContext\OperationType;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DebitOperationsHandlerTest extends HandlerBaseTestCase
{
    #[DataProvider('getCorrectData')]
    #[Test]
    public function shouldHandleDebit(
        string $testId,
        Debit $command,
        BankAccountOperation $expectedEntity,
        BankAccountOperationCollection $operations,
        float $operationCost,
        float $balance,
    ): void {
        $this->repository->expects($this->once())->method('addBankAccountOperation')->with($expectedEntity);
        $this->repository->expects($this->once())->method('getBankAccountOperations')->willReturn($operations);

        $this->idProvider->expects($this->once())->method('getId')->willReturn($testId);

        $this->costCalculator->expects($this->exactly(3))->method('getDebitOperationCost')->willReturn($operationCost);
        $this->balanceCalculator->expects($this->once())->method('getBalance')->with($operations)->willReturn($balance);

        $sut = new DebitOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
            $this->balanceCalculator
        );
        $sut->handle($command);
    }

    #[DataProvider('getDataInsufficientFundsData')]
    #[Test]
    public function shouldThrowExceptionWhenBalanceIsNotSufficient(
        Debit $command,
        BankAccountOperationCollection $operations,
        float $operationCost,
        float $balance
    ): void {
        $this->repository->expects($this->once())->method('getBankAccountOperations')->willReturn($operations);

        $this->costCalculator->expects($this->once())->method('getDebitOperationCost')->willReturn($operationCost);
        $this->balanceCalculator->expects($this->once())->method('getBalance')->with($operations)->willReturn($balance);

        $sut = new DebitOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
            $this->balanceCalculator
        );

        $this->expectException(InsufficientFunds::class);
        $sut->handle($command);
    }

    #[DataProvider('getCorrectDataWithLimit')]
    #[Test]
    public function shouldThrowExceptionWhenDebitLimitReached(
        Debit $command,
        BankAccountOperationCollection $operations,
        float $operationCost,
        float $balance,
        int $limit
    ): void {
        $this->repository->expects($this->once())->method('getBankAccountOperations')->willReturn($operations);
        $this->repository->expects($this->once())->method('getOperationsCountByDate')->willReturn($limit);

        $this->costCalculator->expects($this->once())->method('getDebitOperationCost')->willReturn($operationCost);
        $this->balanceCalculator->expects($this->once())->method('getBalance')->with($operations)->willReturn($balance);

        $sut = new DebitOperationHandler(
            $this->repository,
            $this->idProvider,
            $this->costCalculator,
            $this->balanceCalculator
        );

        $this->expectException(OperationsLimit::class);
        $sut->handle($command);
    }

    public static function getCorrectData(): array
    {
        $testId = 'test-id';
        $operationDate = new DateTime();

        $operations = new BankAccountOperationCollection();
        $operations->add(
            new BankAccountOperation(
                '1234',
                OperationType::CREDIT,
                Currency::EUR,
                2000.00,
                0.00,
                0.00,
                2000.00,
                $operationDate
            )
        );

        return [
            [
                $testId,
                new Debit(
                    OperationType::DEBIT,
                    new BankAccount($testId, Currency::EUR),
                    1000.00,
                    Currency::EUR,
                    $operationDate
                ),
                new BankAccountOperation(
                    $testId,
                    OperationType::DEBIT,
                    Currency::EUR,
                    1000.00,
                    50.00,
                    0.05,
                    1050.00,
                    $operationDate
                ),
                $operations,
                50.00,
                2000.00
            ],
        ];
    }

    public static function getDataInsufficientFundsData(): array
    {
        $testId = 'test-id';
        $operationDate = new DateTime();

        $operations = new BankAccountOperationCollection();
        $operations->add(
            new BankAccountOperation(
                '1234',
                OperationType::CREDIT,
                Currency::EUR,
                500.00,
                0.00,
                0.00,
                2000.00,
                $operationDate
            )
        );

        return [
            [
                new Debit(
                    OperationType::DEBIT,
                    new BankAccount($testId, Currency::EUR),
                    1000.00,
                    Currency::EUR,
                    $operationDate
                ),
                $operations,
                50.00,
                500.00,
            ],
        ];
    }

    public static function getCorrectDataWithLimit(): array
    {
        $testId = 'test-id';
        $operationDate = new DateTime();

        $operations = new BankAccountOperationCollection();
        $operations->add(
            new BankAccountOperation(
                '1234',
                OperationType::CREDIT,
                Currency::EUR,
                2000.00,
                0.00,
                0.00,
                2000.00,
                $operationDate
            )
        );

        return [
            [
                new Debit(
                    OperationType::DEBIT,
                    new BankAccount($testId, Currency::EUR),
                    1000.00,
                    Currency::EUR,
                    $operationDate
                ),
                $operations,
                50.00,
                2000.00,
                3
            ],
        ];
    }
}
