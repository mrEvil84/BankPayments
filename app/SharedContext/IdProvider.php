<?php

declare(strict_types=1);

namespace App\SharedContext;

interface IdProvider
{
    public function getId(): string;
}
