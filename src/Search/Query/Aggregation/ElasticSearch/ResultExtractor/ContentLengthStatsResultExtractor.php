<?php

declare(strict_types=1);

namespace App\Search\Query\Aggregation\ElasticSearch\ResultExtractor;

use App\Search\Query\Aggregation\ContentLengthStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;
use Ibexa\Platform\Contracts\ElasticSearchEngine\Query\AggregationResultExtractor;
use Ibexa\Platform\Contracts\ElasticSearchEngine\Query\LanguageFilter;

final class ContentLengthStatsResultExtractor implements AggregationResultExtractor
{
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof ContentLengthStatsAggregation;
    }

    public function extract(Aggregation $aggregation, LanguageFilter $languageFilter, array $data): AggregationResult
    {
        return new StatsAggregationResult(
            $aggregation->getName(),
            $data['count'] ?? null,
            $data['min'] ?? null,
            $data['max'] ?? null,
            $data['avg'] ?? null,
            $data['sum'] ?? null,
        );
    }
}
