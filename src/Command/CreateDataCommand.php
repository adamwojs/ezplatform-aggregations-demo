<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Faker\Factory as FakerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateDataCommand extends Command
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    private $faker;

    public function __construct(Repository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->faker = FakerFactory::create();
    }

    protected function configure(): void
    {
        $this->setName('ezplatform:aggregation-demo:create-data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin('admin')
        );

        $this->createDataForTagCloudExample();
        $this->createDataForProductReviewExample();

        return self::SUCCESS;
    }

    private function createDataForProductReviewExample(): void
    {
        $contentService = $this->repository->getContentService();
        $locationService = $this->repository->getLocationService();

        $productContentType = $this->getProductContentType();

        $productContentCreateStruct = $contentService->newContentCreateStruct($productContentType, 'eng-GB');
        $productContentCreateStruct->alwaysAvailable = false;
        $productContentCreateStruct->setField('name', 'Example product');

        $draft = $contentService->createContent(
            $productContentCreateStruct,
            [
                $locationService->newLocationCreateStruct(2),
            ]
        );

        $product = $contentService->publishVersion($draft->getVersionInfo());

        $reviewContentType = $this->getReviewContentType();
        for ($i = 0; $i < 10; $i++) {
            $reviewContentCreateStruct = $contentService->newContentCreateStruct($reviewContentType, 'eng-GB');
            $reviewContentCreateStruct->alwaysAvailable = false;
            $reviewContentCreateStruct->setField('author', $this->faker->name);
            $reviewContentCreateStruct->setField('comment', $this->faker->paragraph);
            $reviewContentCreateStruct->setField('rate', $this->faker->numberBetween(1, 10));

            $draft = $contentService->createContent(
                $reviewContentCreateStruct,
                [
                    $locationService->newLocationCreateStruct(
                        $product->contentInfo->mainLocationId
                    ),
                ]
            );

            $contentService->publishVersion($draft->getVersionInfo());
        }
    }


    private function getProductContentType(): ContentType
    {
        $contentTypeService = $this->repository->getContentTypeService();

        try {
            return $contentTypeService->loadContentTypeByIdentifier('product');
        } catch (NotFoundException $e) {
            $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('product');
            $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
            $contentTypeCreateStruct->names = ['eng-GB' => 'Product'];
            $contentTypeCreateStruct->creatorId = 14;
            $contentTypeCreateStruct->creationDate = new \DateTime();
            $contentTypeCreateStruct->isContainer = true;

            $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('name', 'ezstring');
            $fieldCreate->names = ['eng-GB' => 'Name'];
            $fieldCreate->fieldGroup = 'main';
            $fieldCreate->position = 1;

            $contentTypeCreateStruct->addFieldDefinition($fieldCreate);

            $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
            $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentGroup]);
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);

            return $contentTypeService->loadContentType($contentTypeDraft->id);
        }
    }

    private function getReviewContentType(): ContentType
    {
        $contentTypeService = $this->repository->getContentTypeService();

        try {
            return $contentTypeService->loadContentTypeByIdentifier('review');
        } catch (NotFoundException $e) {
            $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('review');
            $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
            $contentTypeCreateStruct->names = ['eng-GB' => 'Review'];
            $contentTypeCreateStruct->creatorId = 14;
            $contentTypeCreateStruct->creationDate = new \DateTime();

            $authorFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('author', 'ezstring');
            $authorFieldCreate->names = ['eng-GB' => 'Author'];
            $authorFieldCreate->position = 1;
            $authorFieldCreate->fieldGroup = 'main';

            $commentFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('comment', 'ezstring');
            $commentFieldCreate->names = ['eng-GB' => 'Comment'];
            $commentFieldCreate->position = 2;
            $commentFieldCreate->fieldGroup = 'main';

            $rateFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('rate', 'ezinteger');
            $rateFieldCreate->names = ['eng-GB' => 'Rate'];
            $rateFieldCreate->position = 3;
            $rateFieldCreate->fieldGroup = 'main';

            $contentTypeCreateStruct->addFieldDefinition($authorFieldCreate);
            $contentTypeCreateStruct->addFieldDefinition($commentFieldCreate);
            $contentTypeCreateStruct->addFieldDefinition($rateFieldCreate);

            $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
            $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentGroup]);
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);

            return $contentTypeService->loadContentType($contentTypeDraft->id);
        }
    }

    private function createDataForTagCloudExample(): void
    {
        $contentTypeService = $this->repository->getContentTypeService();

        try {
            $contentType = $contentTypeService->loadContentTypeByIdentifier('code_snippet');
        } catch (NotFoundException $e) {
            $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('code_snippet');
            $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
            $contentTypeCreateStruct->names = ['eng-GB' => 'Code snippet'];
            $contentTypeCreateStruct->creatorId = 14;
            $contentTypeCreateStruct->creationDate = new \DateTime();

            $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
            $fieldCreate->names = ['eng-GB' => 'title'];
            $fieldCreate->fieldGroup = 'main';
            $fieldCreate->position = 1;

            $contentTypeCreateStruct->addFieldDefinition($fieldCreate);

            $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('keywords', 'ezkeyword');
            $fieldCreate->names = ['eng-GB' => 'Keywords'];
            $fieldCreate->fieldGroup = 'main';
            $fieldCreate->position = 2;

            $contentTypeCreateStruct->addFieldDefinition($fieldCreate);

            $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
            $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentGroup]);
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);
            $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);
        }

        $contentService = $this->repository->getContentService();
        $locationService = $this->repository->getLocationService();

        $tags = [
            'PHP', 'Java', 'XML', 'JavaScript', 'Ruby', 'Python', 'C', 'Go', 'Dart', 'TypeScript', 'Prolog',
            'Pascal', 'HTML', 'SVG', 'C#', 'C++', 'Cobol', 'Brain F**k', 'Whitespace', 'Kotlin'
        ];

        for ($i = 1; $i <= 10; $i++) {
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $contentCreateStruct->alwaysAvailable = false;
            $contentCreateStruct->setField('title', 'Code snippet ' . $i);

            $contentCreateStruct->setField('keywords', array_map(function ($idx) use ($tags) {
                return $tags[$idx];
            }, array_rand($tags, rand(3, count($tags)))));

            $draft = $contentService->createContent(
                $contentCreateStruct,
                [
                    $locationService->newLocationCreateStruct(2),
                ]
            );

            $contentService->publishVersion($draft->getVersionInfo());
        }
    }
}
