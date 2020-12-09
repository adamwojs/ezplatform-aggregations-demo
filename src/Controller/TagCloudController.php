<?php

declare(strict_types=1);

namespace App\Controller;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Field\KeywordTermAggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TagCloudController extends AbstractController
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @Route("/tag-cloud", name="tag_cloud")
     */
    public function renderAction(): Response
    {
        $aggregation = new KeywordTermAggregation('tags', 'code_snippet', 'keywords');
        $aggregation->setLimit(100);
        $aggregation->setMinCount(3);

        $query = new Query();
        $query->limit = 0;
        $query->filter = new ContentTypeIdentifier('code_snippet');
        $query->aggregations[] = $aggregation;

//         dump($query);

        $results = $this->searchService->findContent($query);

//         dump($results);

        return $this->render('@ezdesign/tag_cloud/show.html.twig', [
            'tags' => $results->aggregations->get('tags'),
        ]);
    }
}
