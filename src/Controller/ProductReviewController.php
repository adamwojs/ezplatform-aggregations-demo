<?php

declare(strict_types=1);

namespace App\Controller;

use App\QueryType\ProductReviewsQueryType;
use eZ\Publish\API\Repository\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ProductReviewController extends AbstractController
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \App\QueryType\ProductReviewsQueryType */
    private $queryType;

    public function __construct(SearchService $searchService, ProductReviewsQueryType $queryType)
    {
        $this->searchService = $searchService;
        $this->queryType = $queryType;
    }

    public function renderReviews(int $locationId): Response
    {
        $query = $this->queryType->getQuery([
            'product' => $locationId
        ]);

        // dump($query);

        $results = $this->searchService->findContent($query);

        // dump($results)

        return $this->render(
            '@ezdesign/product/_reviews.html.twig',
            [
                'reviews' => $results
            ]
        );
    }
}
