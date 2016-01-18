<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Document\Subscriber;

use Sulu\Component\CustomUrl\Manager\CustomUrlManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles document-manager events for custom-urls.
 */
class CustomUrlSubscriber implements EventSubscriberInterface
{
    /**
     * @var CustomUrlManagerInterface
     */
    private $customUrlManager;

    public function __construct(CustomUrlManagerInterface $customUrlManager)
    {
        $this->customUrlManager = $customUrlManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        ];
    }
}
