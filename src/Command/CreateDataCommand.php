<?php

declare(strict_types=1);

namespace App\Command;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateDataCommand extends Command
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
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

        return self::SUCCESS;
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
