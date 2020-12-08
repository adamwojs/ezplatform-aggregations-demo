<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\ContentTypeTermAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\LanguageTermAggregation;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SandboxCommand extends AbstractRepositoryCommand
{
    public function __construct(Repository $repository)
    {
        parent::__construct($repository, 'ezplatform:aggregation-demo:sandbox');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $searchService = $this->repository->getSearchService();

        $query = new Query();
        // Attach your aggregation here:
        $query->aggregations[] = new ContentTypeTermAggregation('content_by_type');
        // Multiple aggregations could be computed in one query
        $query->aggregations[] = new LanguageTermAggregation('content_by_language');

        // Aggregation results respects filter/query ...
        // $query->filter = new ContentTypeIdentifier(['folder', 'article', 'review']);

        // ... but ignores limit and offset
        // $query->offset = 0;
        // $query->limit = 0;

        $results = $searchService->findContent($query);

        // Aggregation results are accessible via $results->aggregations property

        // Display result of the first aggregation
        // $this->renderContentTypeTermAggregation($io, $results->aggregations->first());
        // Access results by aggregation name
        // $this->renderLanguageTermAggregation($io, $results->aggregations->get('content_by_language'));

        return self::SUCCESS;
    }

    private function renderContentTypeTermAggregation(OutputStyle $io, TermAggregationResult $result): void
    {
        $rows = [];
        foreach ($result as $contentType => $count) {
            /* @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
            $rows[] = [$contentType->identifier, $count];
        }

        $io->table(['content type', 'count'], $rows);
    }

    private function renderLanguageTermAggregation(OutputStyle $io, TermAggregationResult $result): void
    {
        $rows = [];
        foreach ($result as $language => $count) {
            /* @var \eZ\Publish\API\Repository\Values\Content\Language $language */
            $rows[] = [$language->name, $count];
        }

        $io->table(['content type', 'count'], $rows);
    }
}
