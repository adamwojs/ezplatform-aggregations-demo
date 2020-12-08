<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawTermAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RepositoryMetricsCommand extends AbstractRepositoryCommand
{
    public function __construct(Repository $repository)
    {
        parent::__construct($repository, 'ezplatform:aggregation-demo:metrics');
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('subtree', null, InputOption::VALUE_REQUIRED);
        $this->addOption('content-type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $filter = $this->buildMetricsFilter($input);

        $locationMetrics = $this->getLocationMetrics($filter);
        $contentMetrics = $this->getContentMetrics($filter);

        $io = new SymfonyStyle($input, $output);

        if (!$locationMetrics->aggregations->isEmpty()) {
            $io->section('Location metrics');
            $io->table(
                ['metric', 'min', 'max', 'avg'],
                [
                    [
                        'depth',
                        $locationMetrics->aggregations->get('depth')->getMin(),
                        $locationMetrics->aggregations->get('depth')->getMax(),
                        $locationMetrics->aggregations->get('depth')->getAvg()
                    ],
                    [
                        'priority',
                        $locationMetrics->aggregations->get('priority')->getMin(),
                        $locationMetrics->aggregations->get('priority')->getMax(),
                        $locationMetrics->aggregations->get('priority')->getAvg()
                    ],
                ]
            );
        }

        if (!$contentMetrics->aggregations->isEmpty()) {
            $io->section('Content metrics');
            $io->table(
                ['metric', 'min', 'max', 'avg'],
                [
                    [
                        'version',
                        $contentMetrics->aggregations->get('version')->getMin(),
                        $contentMetrics->aggregations->get('version')->getMax(),
                        $contentMetrics->aggregations->get('version')->getAvg()
                    ],
                ]
            );

            $io->section('Language stats');
            $io->table(
                ['language', 'translation %'],
                array_map(static function (TermAggregationResultEntry $entry) use ($contentMetrics) {
                    return [
                        $entry->getKey(),
                        round(($entry->getCount() / $contentMetrics->totalCount) * 100, 2) . '%'
                    ];
                }, $contentMetrics->aggregations->get('language')->getEntries())
            );
        }

        return self::SUCCESS;
    }

    private function getLocationMetrics(Criterion $filter): SearchResult
    {
        return $this->repository->getSearchService()->findLocations(
            $this->createLocationMetricsQuery($filter)
        );
    }

    private function getContentMetrics(Criterion $filter): SearchResult
    {
        return $this->repository->getSearchService()->findContent(
            $this->createContentMetricsQuery($filter)
        );
    }
    
    private function createLocationMetricsQuery(Criterion $filter): LocationQuery
    {
        $metricsQuery = new LocationQuery();
        $metricsQuery->filter = $filter;
        $metricsQuery->limit = 0;
        $metricsQuery->aggregations[] = new RawStatsAggregation('depth', 'depth_i');
        $metricsQuery->aggregations[] = new RawStatsAggregation('priority', 'priority_i');

        return $metricsQuery;
    }

    private function createContentMetricsQuery(Criterion $filter): Query
    {
        $metricsQuery = new Query();
        $metricsQuery->filter = $filter;
        $metricsQuery->limit = 0;
        $metricsQuery->aggregations[] = new RawStatsAggregation('version', 'content_version_no_i');
        $metricsQuery->aggregations[] = new RawTermAggregation('language', 'content_language_codes_raw_mid');

        return $metricsQuery;
    }

    private function buildMetricsFilter(InputInterface $input): Criterion
    {
        $criteria = [];

        if (!empty($input->getOption('subtree'))) {
            $criteria[] = new Criterion\Subtree($input->getOption('subtree'));
        }

        if (!empty($input->getOption('content-type'))) {
            $criteria[] = new Criterion\ContentTypeIdentifier($input->getOption('content-type'));
        }

        if (empty($criteria)) {
            return new MatchAll();
        }

        return new Criterion\LogicalAnd($criteria);
    }
}
