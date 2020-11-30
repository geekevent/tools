<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RoleCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRoleCommand extends Command
{
    protected static $defaultName = 'app:create:role';

    private RoleCreator $creator;

    /**
     * LoadModuleCommand constructor.
     */
    public function __construct(RoleCreator $creator)
    {
        parent::__construct();
        $this->creator = $creator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->creator->run();

        return 0;
    }
}
