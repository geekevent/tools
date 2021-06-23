<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ModuleCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadModuleCommand extends Command
{
    protected static $defaultName = 'app:create:module';

    private ModuleCreator $creator;

    /**
     * LoadModuleCommand constructor.
     */
    public function __construct(ModuleCreator $creator)
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
