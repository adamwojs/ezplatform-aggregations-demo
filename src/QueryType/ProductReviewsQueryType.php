<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\QueryType;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Field\IntegerStatsAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductReviewsQueryType extends OptionsResolverBasedQueryType
{
    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired('product');
    }

    protected function doGetQuery(array $parameters): Query
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $product */
        $product = $parameters['product'];
        if ($product instanceof Content) {
            $product = $product->contentInfo->getMainLocation()->id;
        }

        $query = new Query();
        $query->limit = 10;
        $query->filter = new Query\Criterion\LogicalAnd([
            new ParentLocationId($product),
            new ContentTypeIdentifier('review')
        ]);

        $query->sortClauses[] = new DatePublished(Query::SORT_DESC);
        $query->aggregations[] = new IntegerStatsAggregation('rating', 'review', 'rate');

        return $query;
    }

    public static function getName(): string
    {
        return 'app:product_reviews';
    }
}
