<?php

declare(strict_types=1);

namespace App\Search\Query\Aggregation\Solr\Visitor;

use App\Search\Query\Aggregation\ContentLengthStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use EzSystems\EzPlatformSolrSearchEngine\Query\AggregationVisitor;

final class ContentLengthStatsVisitor implements AggregationVisitor
{
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof ContentLengthStatsAggregation;
    }

    public function visit(AggregationVisitor $dispatcherVisitor, Aggregation $aggregation, array $languageFilter): array
    {
        $field = 'content_length_i';

        return [
            'type' => 'query',
            'q' => '*:*',
            'facet' => [
                'sum' => "sum($field)",
                'min' => "min($field)",
                'max' => "max($field)",
                'avg' => "avg($field)",
            ],
        ];
    }
}
