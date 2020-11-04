<?php

declare(strict_types=1);

namespace App\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\QueryType\BuiltIn\SortClausesFactoryInterface;
use eZ\Publish\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SortSpecDemoQueryType extends OptionsResolverBasedQueryType
{
    /** @var \eZ\Publish\Core\QueryType\BuiltIn\SortClausesFactoryInterface */
    private $sortClausesFactory;

    public function __construct(SortClausesFactoryInterface $sortClausesFactory)
    {
        $this->sortClausesFactory = $sortClausesFactory;
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults([
            'sort_by' => null,
        ]);
    }

    protected function doGetQuery(array $parameters): Query
    {
        $query = new Query();
        $query->sortClauses = $this->sortClausesFactory->createFromSpecification(
            $parameters['sort_by']
        );

        return $query;
    }

    public static function getName(): string
    {
        return 'App:SortSpecDemo';
    }
}
