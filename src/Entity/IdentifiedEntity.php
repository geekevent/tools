<?php

namespace App\Entity;

interface IdentifiedEntity
{
    public function getIdentifier(): string;
}
