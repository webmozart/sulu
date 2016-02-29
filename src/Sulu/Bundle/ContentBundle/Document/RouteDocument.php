<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContentBundle\Document;

use Sulu\Component\Content\Document\Behavior\RouteBehavior;
use Sulu\Component\DocumentManager\Behavior\Audit\NonLocalizedTimestampBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\NodeNameBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\PathBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\UuidBehavior;

/**
 * The route document represents a route with in a webspace.
 *
 * Route Documents are children of the designated route-containing
 * node (which is a child of the webspace node).
 *
 * Routes contain a reference to the content which should be displayed
 * when the route is resolved by the RouteProvider.
 */
class RouteDocument implements
    NodeNameBehavior,
    PathBehavior,
    UuidBehavior,
    RouteBehavior,
    NonLocalizedTimestampBehavior
{
    /**
     * @var string
     */
    private $nodeName;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var object
     */
    private $targetDocument;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var bool
     */
    private $history;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $changed;

    /**
     * {@inheritdoc}
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetDocument()
    {
        return $this->targetDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetDocument($targetDocument)
    {
        $this->targetDocument = $targetDocument;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function isHistory()
    {
        return $this->history;
    }

    /**
     * {@inheritdoc}
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * {@inheritdoc}
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param \DateTime $changed
     */
    public function setChanged(\DateTime $changed)
    {
        $this->changed = $changed;
    }
}
