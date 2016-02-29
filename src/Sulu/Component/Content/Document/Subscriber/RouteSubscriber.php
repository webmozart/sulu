<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Content\Document\Subscriber;

use Sulu\Component\Content\Document\Behavior\RouteBehavior;
use Sulu\Component\DocumentManager\Event\MetadataLoadEvent;
use Sulu\Component\DocumentManager\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Behavior for route (sulu:path) documents.
 */
class RouteSubscriber implements EventSubscriberInterface
{
    const DOCUMENT_TARGET_FIELD = 'content';
    const DOCUMENT_LOCALE_FIELD = 'locale';

    public static function getSubscribedEvents()
    {
        return [
            Events::METADATA_LOAD => 'handleMetadataLoad',
        ];
    }

    public function handleMetadataLoad(MetadataLoadEvent $event)
    {
        $metadata = $event->getMetadata();

        if (false === $metadata->getReflectionClass()->isSubclassOf(RouteBehavior::class)) {
            return;
        }

        $metadata->addFieldMapping('targetDocument', [
            'encoding' => 'system',
            'property' => self::DOCUMENT_TARGET_FIELD,
            'type' => 'reference',
        ]);
        $metadata->addFieldMapping('locale', [
            'encoding' => 'system',
            'property' => self::DOCUMENT_LOCALE_FIELD,
        ]);
    }
}
