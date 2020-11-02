<?php

declare(strict_types=1);

namespace App\Search\Query\Aggregation\ElasticSearch\Visitor;

use App\Search\Query\Aggregation\ContentLengthStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use Ibexa\Platform\Contracts\ElasticSearchEngine\Query\AggregationVisitor;
use Ibexa\Platform\Contracts\ElasticSearchEngine\Query\LanguageFilter;

final class ContentLengthStatsVisitor implements AggregationVisitor
{
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof ContentLengthStatsAggregation;
    }

    public function visit(AggregationVisitor $dispatcher, Aggregation $aggregation, LanguageFilter $languageFilter): array
    {
        return [
            'stats' => [
                'field' => 'content_length_i',
            ],
        ];
    }
}
