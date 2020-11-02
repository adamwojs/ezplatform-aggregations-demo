<?php

declare(strict_types=1);

namespace App\Search\Query\Aggregation\Solr\ResultExtractor;

use App\Search\Query\Aggregation\ContentLengthStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;
use EzSystems\EzPlatformSolrSearchEngine\ResultExtractor\AggregationResultExtractor;
use stdClass;

final class ContentLengthStatsResultExtractor implements AggregationResultExtractor
{
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof ContentLengthStatsAggregation;
    }

    public function extract(Aggregation $aggregation, array $languageFilter, stdClass $data): AggregationResult
    {
        return new StatsAggregationResult(
            $aggregation->getName(),
            $data->count ?? null,
            $data->min ?? null,
            $data->max ?? null,
            $data->avg ?? null,
            $data->sum ?? null,
        );
    }
}
