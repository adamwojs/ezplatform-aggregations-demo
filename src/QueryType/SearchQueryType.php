<?php

declare(strict_types=1);

namespace App\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchQueryType extends OptionsResolverBasedQueryType
{
    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'query' => '',
            'content_type' => null,
            'section' => null,
            'limit' => 10
        ]);
    }

    protected function doGetQuery(array $parameters): Query
    {
        $query = new Query();

        if (!empty($parameters['query'])) {
            $query->query = new Criterion\FullText($parameters['query']);
        }

        // (!) Attach aggregation definition to query
        $query->aggregations[] = new Aggregation\ContentTypeTermAggregation('content_types');
        // (!) Single query can compute multiple aggregations as once
        // $query->aggregations[] = new Aggregation\SectionTermAggregation('sections');

        $query->filter = $this->buildFilters($parameters);
        $query->limit = $parameters['limit'];

        return $query;
    }

    private function buildFilters(array $parameters): ?Criterion
    {
        $filters = [];

        if ($parameters['content_type'] !== null) {
            $filters[] = new Criterion\ContentTypeId($parameters['content_type']);
        }

//        if ($parameters['section'] !== null) {
//            $filters[] = new Criterion\SectionId($parameters['section']);
//        }

        if (empty($filters)) {
            return null;
        }

        if (count($filters) > 1) {
            return new Criterion\LogicalAnd($filters);
        }

        return $filters[0];
    }

    public static function getName(): string
    {
        return 'app:search';
    }
}
