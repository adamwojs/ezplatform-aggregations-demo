<?php

declare(strict_types=1);

namespace App\Controller;

use App\QueryType\SearchQueryType;
use eZ\Publish\API\Repository\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Search controller.
 */
final class SearchController extends AbstractController
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \App\QueryType\SearchQueryType */
    private $searchQueryType;

    public function __construct(
        SearchService $searchService,
        SearchQueryType $searchQueryType
    ) {
        $this->searchService = $searchService;
        $this->searchQueryType = $searchQueryType;
    }

    /**
     * @Route("/facet-search", name="custom_search")
     */
    public function searchAction(Request $request): Response
    {
        $searchQuery = $this->searchQueryType->getQuery(
            $this->getQueryParameters($request)
        );

        // dump($searchQuery);

        $results = $this->searchService->findContent($searchQuery);

//         dump($results);

        return $this->render('@ezdesign/search/index.html.twig', [
            'results' => $results
        ]);
    }

    private function getQueryParameters(Request $request): array
    {
        // Skip validation for the sake of example simplicity
        return $request->query->all();
    }
}
