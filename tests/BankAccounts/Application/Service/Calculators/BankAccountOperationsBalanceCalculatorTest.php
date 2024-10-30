<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service\Calculators;

use App\BankAccounts\Application\Service\Calculators\BankAccountOperationsBalanceCalculator;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\Entity\BankAccountOperationCollection;
use App\BankAccounts\SharedContext\Currency;
use App\BankAccounts\SharedContext\OperationType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BankAccountOperationsBalanceCalculatorTest extends TestCase
{
    private BankAccountOperationsBalanceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new BankAccountOperationsBalanceCalculator();
    }

    #[DataProvider('getOperations')]
    #[Test]
    public function shouldGetBalance(BankAccountOperationCollection $operations, float $expectedBalance): void
    {
        $actualBalance = $this->calculator->getBalance($operations);
        $this->assertSame($expectedBalance, $actualBalance);
    }

    public static function getOperations(): array
    {
        $collectionCredit = new BankAccountOperationCollection();
        $collectionCredit->add(new BankAccountOperation(
            'abcd-1',
            OperationType::CREDIT,
            Currency::EUR,
            1000.00,
            0.00,
            0.00,
            1000.00,
            new \DateTime()
        ));
        $collectionCredit->add(new BankAccountOperation(
            'abcd-2',
            OperationType::CREDIT,
            Currency::EUR,
            1000.00,
            0.00,
            0.00,
            1000.00,
            new \DateTime()
        ));

        $creditDebit  = new BankAccountOperationCollection();
        $creditDebit->add(new BankAccountOperation(
            'abcd-1',
            OperationType::CREDIT,
            Currency::EUR,
            1000.00,
            0.00,
            0.00,
            1000.00,
            new \DateTime()
        ));
        $creditDebit->add(new BankAccountOperation(
            'abcd-2',
            OperationType::CREDIT,
            Currency::EUR,
            1000.00,
            0.00,
            0.00,
            1000.00,
            new \DateTime()
        ));
        $creditDebit->add(new BankAccountOperation(
            'abcd-3',
            OperationType::DEBIT,
            Currency::EUR,
            2000.00,
            100.00,
            0.05,
            2100.00,
            new \DateTime()
        ));

        $collectionDebit = new BankAccountOperationCollection();
        $collectionDebit->add(new BankAccountOperation(
            'abcd-1',
            OperationType::DEBIT,
            Currency::EUR,
            2000.00,
            100.00,
            0.05,
            2100.00,
            new \DateTime()
        ));
        $collectionDebit->add(new BankAccountOperation(
            'abcd-2',
            OperationType::DEBIT,
            Currency::EUR,
            2000.00,
            100.00,
            0.05,
            2100.00,
            new \DateTime()
        ));

        return [
            [$collectionCredit, 2000.00],
            [$creditDebit, -100.00],
            [$collectionDebit, -4200.00],
        ];
    }
}
