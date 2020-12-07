<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SandboxCommand extends AbstractRepositoryCommand
{
    public function __construct(Repository $repository)
    {
        parent::__construct($repository, 'ezplatform:aggregation-demo:sandbox');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $searchService = $this->repository->getSearchService();

        $query = new Query();
        // Add your aggregation here:
        // $query->aggregations[] = ...

        // We don't need search hits
        $query->limit = 0;

        $results = $searchService->findContent($query);

        if (!$results->aggregations->isEmpty()) {
            // Display result of the first aggregation
            dump($results->aggregations->first());
        }

        return self::SUCCESS;
    }
}
