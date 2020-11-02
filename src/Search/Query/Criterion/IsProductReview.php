<?php

declare(strict_types=1);

namespace App\Search\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\CompositeCriterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;

final class IsProductReview extends CompositeCriterion
{
    public function __construct()
    {
        parent::__construct(new ContentTypeIdentifier('review'));
    }
}
