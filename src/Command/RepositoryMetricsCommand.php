<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawTermAggregation;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RepositoryMetricsCommand extends AbstractRepositoryCommand
{
    public function __construct(Repository $repository)
    {
        parent::__construct($repository, 'ezplatform:aggregation-demo:metrics');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->repository->getSearchService()->findLocations(
            $this->createLocationMetricsQuery()
        );

        $io = new SymfonyStyle($input, $output);
        $io->section('Location metrics');
        $io->table(
            ['metric', 'min', 'max', 'avg'],
            [
                [
                    'depth',
                    $results->aggregations->get('depth')->getMin(),
                    $results->aggregations->get('depth')->getMax(),
                    $results->aggregations->get('depth')->getAvg()
                ],
                [
                    'priority',
                    $results->aggregations->get('priority')->getMin(),
                    $results->aggregations->get('priority')->getMax(),
                    $results->aggregations->get('priority')->getAvg()
                ],
            ]
        );

        $results = $this->repository->getSearchService()->findContent(
            $this->createContentMetricsQuery()
        );

        $io->section('Content metrics');
        $io->table(
            ['metric', 'min', 'max', 'avg'],
            [
                [
                    'version',
                    $results->aggregations->get('version')->getMin(),
                    $results->aggregations->get('version')->getMax(),
                    $results->aggregations->get('version')->getAvg()
                ],
            ]
        );

        $io->section('Language stats');
        $io->table(
            ['language', 'translation %'],
            array_map(static function (TermAggregationResultEntry $entry) use ($results) {
                return [
                    $entry->getKey(),
                    round(($entry->getCount() / $results->totalCount) * 100, 2) . '%'
                ];
            }, $results->aggregations->get('language')->getEntries())
        );


        return self::SUCCESS;
    }

    private function createLocationMetricsQuery(): LocationQuery
    {
        $metricsQuery = new LocationQuery();
        $metricsQuery->limit = 0;
        $metricsQuery->aggregations[] = new RawStatsAggregation('depth', 'depth_i');
        $metricsQuery->aggregations[] = new RawStatsAggregation('priority', 'priority_i');

        return $metricsQuery;
    }

    private function createContentMetricsQuery(): Query
    {
        $metricsQuery = new Query();
        $metricsQuery->limit = 0;
        $metricsQuery->aggregations[] = new RawStatsAggregation('version', 'content_version_no_i');
        $metricsQuery->aggregations[] = new RawTermAggregation('language', 'content_language_codes_raw_mid');

        return $metricsQuery;
    }
}
