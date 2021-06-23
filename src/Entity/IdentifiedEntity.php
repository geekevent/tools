<?php

declare(strict_types=1);

namespace App\Entity;

interface IdentifiedEntity
{
    public function getIdentifier(): string;
}
