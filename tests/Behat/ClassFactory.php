<?php

namespace App\Tests\Behat;

use App\Entity\Account\Module;
use App\Entity\Account\Role;

class ClassFactory
{
    public static function getClass(string $name): string
    {
        switch ($name) {
            case 'role':
                return Role::class;
            case 'module':
                return Module::class;
            default:
                throw new \InvalidArgumentException('unknown name');
        }
    }
}
