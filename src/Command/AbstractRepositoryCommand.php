<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRepositoryCommand extends Command
{
    /** @var \eZ\Publish\API\Repository\Repository */
    protected $repository;

    public function __construct(Repository $repository, string $name)
    {
        parent::__construct($name);

        $this->repository = $repository;
    }

    protected function configure(): void
    {
        $this->addOption('user', 'u', InputOption::VALUE_REQUIRED, '', 'admin');
    }

    protected final function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setCurrentUserReference($input);

        return $this->doExecute($input, $output);
    }

    protected abstract function doExecute(InputInterface $input, OutputInterface $output): int;

    private function setCurrentUserReference(InputInterface $input): void
    {
        $username = $input->getOption('user');

        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin($username)
        );
    }
}