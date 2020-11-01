<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawRangeAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawTermAggregation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SandboxCommand extends Command
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        parent::__construct('ezplatform:aggregation-demo:sandbox');

        $this->repository = $repository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin('admin')
        );

        $searchService = $this->repository->getSearchService();

        $query = new Query();
        $query->limit = 0;
        // Add your aggregation here:
        // $query->aggregations[] = ...

        $results = $searchService->findContent($query);

        dump($results->aggregations->first());

        return self::SUCCESS;
    }
}
