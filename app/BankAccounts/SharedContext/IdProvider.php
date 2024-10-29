<?php

declare(strict_types=1);

namespace App\BankAccounts\SharedContext;

interface IdProvider
{
    public function getId(): string;
}
