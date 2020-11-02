<?php

declare(strict_types=1);

namespace App\Search\FieldMapper;

use eZ\Publish\Core\Search\Common\FieldRegistry;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type\Handler;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType\FullTextField;
use eZ\Publish\SPI\Search\FieldType\IntegerField;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper;

final class ContentLengthMapper extends ContentTranslationFieldMapper
{
    /** @var \eZ\Publish\SPI\Persistence\Content\Type\Handler */
    private $contentTypeHandler;

    /** @var \eZ\Publish\Core\Search\Common\FieldRegistry */
    private $fieldRegistry;

    public function __construct(Handler $contentTypeHandler, FieldRegistry $fieldRegistry)
    {
        $this->contentTypeHandler = $contentTypeHandler;
        $this->fieldRegistry = $fieldRegistry;
    }

    public function accept(SPIContent $content, $languageCode)
    {
        return true;
    }

    public function mapFields(SPIContent $content, $languageCode)
    {
        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        $length = 0;
        foreach ($content->fields as $field) {
            if ($field->languageCode !== $languageCode) {
                continue;
            }

            foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                if ($fieldDefinition->id !== $field->fieldDefinitionId) {
                    continue;
                }

                $fieldType = $this->fieldRegistry->getType($field->type);
                $indexFields = $fieldType->getIndexData($field, $fieldDefinition);

                foreach ($indexFields as $indexField) {
                    if ($indexField->value === null) {
                        continue;
                    }

                    if (!$indexField->type instanceof FullTextField || !$fieldDefinition->isSearchable) {
                        continue;
                    }

                    foreach ((array)$indexField->value as $text) {
                        $length += str_word_count((string)$text);
                    }
                }
            }
        }

        return [
            new Field(
                'content_length',
                $length,
                new IntegerField()
            )
        ];
    }
}
