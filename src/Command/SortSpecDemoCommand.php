<?php

declare(strict_types=1);

namespace App\Command;

use App\QueryType\SortSpecDemoQueryType;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SortSpecDemoCommand extends Command
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \App\QueryType\SortSpecDemoQueryType */
    private $sortSpecDemoQueryType;

    public function __construct(Repository $repository, SortSpecDemoQueryType $sortSpecDemoQueryType)
    {
        parent::__construct('ezplatform:sort-spec-demo');

        $this->repository = $repository;
        $this->sortSpecDemoQueryType = $sortSpecDemoQueryType;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin('admin')
        );

        $query = $this->sortSpecDemoQueryType->getQuery([
            'sort_by' => 'content_id'
        ]);

        $searchService = $this->repository->getSearchService();
        $results = $searchService->findContent($query);

        foreach ($results as $result) {
            /** @var Content $content */
            $content = $result->valueObject;

            $output->writeln($content->getName());
        }

        return self::SUCCESS;
    }

}
