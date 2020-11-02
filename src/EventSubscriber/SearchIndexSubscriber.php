<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use eZ\Publish\SPI\Search\Document;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType\FullTextField;
use eZ\Publish\SPI\Search\FieldType\IntegerField;
use Ibexa\Platform\Contracts\ElasticSearchEngine\Mapping\Event\ContentIndexCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SearchIndexSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ContentIndexCreateEvent::class => ['onContentIndexCreateEvent'],
        ];
    }

    public function onContentIndexCreateEvent(ContentIndexCreateEvent $event): void
    {
        $document = $event->getDocument();
        $document->fields[] = new Field(
            'content_length',
            $this->getContentLength($document),
            new IntegerField()
        );
    }

    private function getContentLength(Document $document): int
    {
        $length = 0;
        foreach ($document->fields as $field) {
            if ($field->type instanceof FullTextField) {
                foreach ((array)$field->value as $text) {
                    $length += str_word_count((string)$text);
                }
            }
        }

        return $length;
    }
}
